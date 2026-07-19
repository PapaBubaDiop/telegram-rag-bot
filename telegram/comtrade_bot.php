<?php
    include '../diop.php';  // Adjust the path as needed if your config.php is outside the public directory
    $botToken = DB_TOKEN_COMTRADE;
    $aiApiKey = AI_TOKEN_COMTRADE;
    $apiUrl = "https://api.telegram.org/bot$botToken/";
    $user = isset($_GET['id']) ? (int)$_GET['id'] : 1967;
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);
    
    if (!$update) {
    // Received data is not valid
        exit;
    }
    
    if (isset($update['message'])) {
        $message = $update['message'];
        
        
        
        
//        handleMessageSimple($message);
//        handleMessageAI($message);
        handleMessageAIwithRAG($message);

    }






function handleMessageSimple($message) {
    global $apiUrl;

    $text = $message['text'];
 
 
    $messageId = $message['message_id'];
    $chatId = $message['chat']['id'];
    $name = $message['from']['first_name'];
    $last = $message['from']['last_name'];
    $user = $message['from']['username'];
    $userId = $message['from']['id'];
    

    switch ($text) {
        case '1':
            sendMessage($chatId, "You sent 1");
            break;
        case '2':
            sendMessage($chatId, "You sent 2. Yes. 2.");
            break;
        default:
            sendMessage($chatId, "Hello ".$name);
    }

}









function handleMessageAI($message) {
    global $apiUrl;

    $messageId = $message['message_id'];
    $chatId = $message['chat']['id'];
    $text = $message['text'];
    $name = $message['from']['first_name'];
    $last = $message['from']['last_name'];
    $user = $message['from']['username'];
    $userId = $message['from']['id'];
    

    switch ($text) {
        case 'who':
            sendMessage($chatId, "hello ".$name," ".$last);
            break;
        default:
            $msg = handleAI($text);
            sendMessage($chatId, $msg);
            break;
    }

}



 
function handleAI($text) {
    global $aiApiKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'gpt-4o',
    'max_tokens' => 100,
   'messages' => [
        ['role' => 'user', 'content' => $text], // Replace with $text or user input
    ],
    ]));


    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '.$aiApiKey
    ]);


    $result = curl_exec($ch);
    curl_close($ch);

// Extract the assistant's answer

    $response = json_decode($result, true);
    $msg = $response['choices'][0]['message']['content'];
    return $msg;
}



function sendMessage($chatId, $text) {
    global $apiUrl;
    
  

     
    

    $params = [
        'chat_id' => $chatId,
        'text' => $text
    ];

    file_get_contents($apiUrl . "sendMessage?" . http_build_query($params));
}






function handleMessageAIwithRAG($message) {
    global $apiUrl;

    $messageId = $message['message_id'];
    $chatId = $message['chat']['id'];
    $text = $message['text'];
    $name = $message['from']['first_name'];
    $last = $message['from']['last_name'];
    $user = $message['from']['username'];
    $userId = $message['from']['id'];
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $final = "";
    $result = $mysql->query("SELECT text FROM bot_history WHERE chat_id ='$chatId' ORDER BY uid DESC LIMIT 3 ");
    while ($row = $result->fetch_assoc()) {
        $last=$row['text'];
        $final = $final . "," . $last;
    }
    $final = $final . "," . $text;
    
     // Retrieve the most relevant document
    $relevantDoc = getRelevantDocument($final);

    if ($relevantDoc) {
        $context = $relevantDoc['content'];
        $augmentedPrompt = "Context: " . $context . "\n\nUser Query: " . $text;
        
        $result = $mysql->query("INSERT INTO bot_history (chat_id, text, content) VALUES ('$chatId', '$text', '$context') ");
    
        if (!$result) {
            die("Query failed: " . $mysql->error);
        }

    
      
        
        
        $msg = handleAI($augmentedPrompt);
        sendMessage($chatId, $msg);
    } else {
        $msg = handleAI($text);
        sendMessage($chatId, $msg);
      }
    $mysql->close();


}


function cosineSimilarity($vec1, $vec2) {
    $dotProduct = 0.0;
    $normA = 0.0;
    $normB = 0.0;
    for ($i = 0; $i < count($vec1); $i++) {
        $dotProduct += $vec1[$i] * $vec2[$i];
        $normA += pow($vec1[$i], 2);
        $normB += pow($vec2[$i], 2);
    }
    return $dotProduct / (sqrt($normA) * sqrt($normB));
}

function getRelevantDocument($query) {
    // Generate embedding for the query
    $queryEmbedding = generateEmbedding($query);
    
    
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = $mysql->query("SELECT id, content, embedding FROM embed_cv");
    if (!$result) {
        die("Query failed: " . $mysql->error);
    }

    $bestMatch = null;
    $highestSimilarity = -1;

    while ($doc = $result->fetch_assoc()) {
        $docEmbedding = json_decode($doc['embedding'], true);
        $similarity = cosineSimilarity($queryEmbedding, $docEmbedding);
        if ($similarity > $highestSimilarity) {
            $highestSimilarity = $similarity;
            $bestMatch = $doc;
        }
    }

    
    $mysql->close();


  

 
    return $bestMatch;
}


function generateEmbedding($text) {
     global $aiApiKey;
     
   
    $ch = curl_init();
   
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/embeddings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
  
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '.$aiApiKey
    ]);


   
    $postData = json_encode([
        'input' => $text,
        'model' => 'text-embedding-ada-002',
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);


   
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    $result = json_decode($response, true);
    return $result['data'][0]['embedding'] ?? null;
}




?>


