<?php
    include '../diop.php';  // Adjust the path as needed if your config.php is outside the public directory
    $botToken = DB_TOKEN_PAPA;
    $apiUrl = "https://api.telegram.org/bot$botToken/";
    $user = isset($_GET['id']) ? (int)$_GET['id'] : 1967;
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);
    if (!$update) {
    // Received data is not valid
        exit;
    }
    if (isset($update['message'])) {
        handleIncomingMessage($update['message']);
    } elseif (isset($update['callback_query'])) {
        handleCallbackQuery($update['callback_query']);
    }

 


function handleIncomingMessageOld($message) {
    global $apiUrl;

    $messageId = $message['message_id'];
    $chatId = $message['chat']['id'];
    $text = $message['text'];
    $firstName = isset($message['from']['first_name']) ? $message['from']['first_name'] : "No first name";
    $userId = $message['from']['id'];


    switch ($text) {
        case 'Hi':
            sendMessage($chatId, 'Hi, type /Play user:'.$userId);
            break;
        case '/Knights':
            sendGame($chatId, 'Knights');
            break;
        case '/Bishops':
    //        sendGame($chatId, 'bishopsGame');
            sendMessage($chatId, 'Чуваки, пардон, игра в разработке, завтра будет доступна.');
            break;
        case 'score':
            $highScores = getGameHighScores($userId, $chatId, $messageId);
            $responseText = formatHighScores($highScores);
            sendMessage($chatId, $responseText);
            break;
        case (preg_match('/^set (\d+)$/', $text, $matches) ? true : false):
            $score = intval($matches[1]);
            $result = setGameScore($userId, $score, null, $chatId, $messageId);
            
            if ($result['ok']) {
                sendMessage($chatId, "Score set to $score for $firstName.");
            } else {
                sendMessage($chatId, "Failed to set score: " . $result['description']. "  message_id=".$messageId);
                logError("SetScore failed", $result, $userId, $chatId, $score, $messageId);
            }
            break;
            
        default:
            sendMessage($chatId, 
            'Type /Knights to play Knights.'
            ."\n\n".
            'Type /Bishops to play Bishops.'
            );
            break;
    }
}




function setUserParams($userId, $tguser, $first, $last) {
    global $apiUrl;
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = $mysql->query("SELECT * FROM users WHERE userid='$userId' LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $mysql->query("UPDATE users SET tg_name = '$tguser', first = '$first', last = '$last'  WHERE userid='$userId'");
    } else {
        $mysql->query("INSERT INTO users (userid, tg_name, first, last) VALUES ('$userId', '$tguser', '$first', '$last' )");
    }
    $mysql->close();
}


function handleIncomingMessage($message) {
    global $apiUrl;

    $messageId = $message['message_id'];
    $chatId = $message['chat']['id'];
    $text = $message['text'];
    $first = $message['from']['first_name'];
    $last = $message['from']['last_name'];
    $tguser = $message['from']['username'];
    $userId = $message['from']['id'];
    
    setUserParams($userId, $tguser, $first, $last);
    

    switch ($text) {
        default:
//            sendMessage($chatId, sendBestScorers($userId) );
            sendMessage($chatId, "hello boy" );
  
            sendGameLinks($chatId, $userId, sendBestScorers($userId));
            break;
    }
}


function sendMessage($chatId, $text) {
    global $apiUrl;
    $params = [
        'chat_id' => $chatId,
        'text' => $text
    ];
    file_get_contents($apiUrl . "sendMessage?" . http_build_query($params));
}



// Function to send game links
function sendGameLinks($chatId, $uid, $text) {
    global $apiUrl;
    
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '👻 Play Knights',  'callback_data' => 'Knights', 'game_short_name' => 'Knights'],
                ['text' => '🧩 Play Bashni',  'callback_data' => 'Bashni', 'game_short_name' => 'Bashni'],
                ['text' => '🔶 Play Pentis',  'callback_data' => 'Pentis', 'game_short_name' => 'Pentis'],
                
            ]
        ]
    ];

    $params = [
        'chat_id' => $chatId,
        'text' => $text,
        'reply_markup' => json_encode($keyboard)
    ];

    file_get_contents($apiUrl . "sendMessage?" . http_build_query($params));
}



// Function to send best scorers
function sendBestScorers($uid) {
    global $apiUrl;
 //   $text = "🏆️ Top\n\n";
    $text = "Top\n\n";
    $index = 0;
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = $mysql->query("SELECT * FROM users ORDER BY knights DESC LIMIT 5");
    if (!$result) {
        die("Query failed: " . $mysql->error);
    }
    $me = 0;
    while ($row = $result->fetch_assoc()) {
        $index  = $index + 1;
        $name=$row['first']." ".$row['last'];
        $pts=$row['knights'];
        if ($row['userid'] == $uid) {
            $text = $text. " ". $index . ". " . $name . " - " . $pts . "\n";
            $me = $index;
        } else {
            $text = $text. " ". $index . ". " . $name . " - " . $pts . "\n";
        }
    }
    if ($me == 0) {
        $result = $mysql->query("SELECT * FROM users WHERE userid='$uid' LIMIT 1");
        if ($row = $result->fetch_assoc()) {
            $s=$row['knights'];
            $name=$row['first']." ".$row['last'];
            $text = $text. " ". $index . ". " . $name . " - " . $s . "\n";
        }    
    }
    $mysql->close();

    return $text;
}



function getRandomInteger($min, $max) {
    return mt_rand($min, $max);
}




function handleCallbackQuery($callbackQuery) {
    global $apiUrl;

    $callbackQueryId = $callbackQuery['id'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $userId = $callbackQuery['from']['id'];
    $data = $callbackQuery['data'];


    switch ($data) {
        case 'Knights':
            sendGame($chatId, $data);
            break;
        case 'Bashni':
            sendGame($chatId, $data);
            break;
        case 'Pentis':
            sendGame($chatId, $data);
            break;
        default:
            $game = $callbackQuery['game_short_name'];
            answerCallbackQuery($callbackQueryId, $userId, $game);
            break;
    }

}
  

function handleCallbackQueryOld($callbackQuery) {
    global $apiUrl;

    $callbackQueryId = $callbackQuery['id'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $messId = $callbackQuery['message']['message_id'];
    $userId = $callbackQuery['from']['id'];
    $username = isset($callbackQuery['from']['username']) ? $callbackQuery['from']['username'] :  $callbackQuery['from']['first_name'];


    $game = isset($callbackQuery['game_short_name']) ? $callbackQuery['game_short_name'] : null;


    answerCallbackQuery($callbackQueryId, $chatId, $userId, $username, $game, $messId);

    // Optionally, send a message to the user with the game link
    // sendMessage($chatId, "Starting game---->".$callbackQueryId);
    
//        $randomId = 490000000 + getRandomInteger(1000000, 10000000);
    // sendMessage($chatId, "User random Id ----> ".$randomId."  score:".$randomScore);
    //    sendMessage($chatId, "game message_id=".$callbackQuery['message']['message_id']);
        
    
    //    $data = $callbackQuery['data'];
    //   sendMessage($chatId, "callbackQuery data".$data);
    
    
//    $highScores = getGameHighScores($userId, $callbackQueryId, $chatId, $callbackQuery['message']['message_id']);
//    $highScores = getGameHighScores($userId, null, $chatId, $callbackQuery['message']['message_id']);
//            $responseText = formatHighScores($highScores);
//            sendMessage($chatId, $responseText);

    
    
    
}




function sendGame($chatId, $data) {
    global $apiUrl;
    $gameShortName = $data;
    $params = [
        'chat_id' => $chatId,
        'game_short_name' => $gameShortName,
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Play', 'callback_game' => json_encode(new stdClass())]]]])
    ];
    file_get_contents($apiUrl . "sendGame?" . http_build_query($params));
}



function answerCallbackQuery($callbackQueryId, $userId, $game) {
    global $apiUrl;
    $r = random_int(1, 1000);
    
    
    switch ($game) {
        case 'Knights':
            $params = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => false,
            'url' => 'https://bashurov.net/2knights/?id='.$userId.'&r='.$r
            ];
            file_get_contents($apiUrl . "answerCallbackQuery?" . http_build_query($params));
            break;
        case 'Bashni':
            $params = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => false,
            'url' => 'https://bashurov.net/bashni/?id='.$userId.'&r='.$r
            ];
            file_get_contents($apiUrl . "answerCallbackQuery?" . http_build_query($params));
            break;
        case 'Pentis':
            $params = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => false,
            'url' => 'https://bashurov.net/pentis/?id='.$userId.'&r='.$r
            ];
            file_get_contents($apiUrl . "answerCallbackQuery?" . http_build_query($params));
            break;
        default:
            break;
    }

    
    
}






function getGameHighScores($userId, $chatId, $messageId) {
    global $apiUrl;
    
     if (!is_numeric($userId) || !is_numeric($chatId) || !is_numeric($messageId)) {
        $error = [
            'ok' => false,
            'description' => 'Invalid parameters for setGameScore'
        ];
        logError("Invalid parameters for setGameScore", $error, $userId, $chatId, $score, $messageId);
        return $error;
    }


    $params = [
        'user_id' => $userId,
        'chat_id' => $chatId,
        'message_id' => $messageId
    ];

    $response = file_get_contents($apiUrl . "getGameHighScores?" . http_build_query($params));
    return json_decode($response, true);
}

function formatHighScores($highScores) {
    if (!isset($highScores['result'])) {
        return "No high scores available.";
    }

    $formatted = "High Scores:\n";
    foreach ($highScores['result'] as $score) {
        $formatted .= $score['position'] . ". " . $score['user']['first_name'];
        if (isset($score['user']['username'])) {
            $formatted .= " (@" . $score['user']['username'] . ")";
        }
        $formatted .= ": " . $score['score'] . "\n";
    }

    return $formatted;
}


function setGameScore($userId, $score, $id, $chatId, $messageId) {
    global $apiUrl;
    
   // $messageId = 288;
    if (!is_numeric($userId) || !is_numeric($score) || !is_numeric($chatId) || !is_numeric($messageId)) {
        $error = [
            'ok' => false,
            'description' => 'Invalid parameters for setGameScore'
        ];
        logError("Invalid parameters for setGameScore", $error, $userId, $chatId, $score, $messageId);
        return $error;
    }


    $params = [
        'user_id' => $userId,
        'score' => $score,
    //   'inline_message_id' => $id,
         'chat_id' => $chatId,
        'message_id' => $messageId,
        'force' => true, // Optional: Force update the score
    //    'disable_edit_message' => false,
    ];

$url = $apiUrl . "setGameScore";
    $response = curlRequest($url, $params);

    // Debugging: Log the full response
    file_put_contents('setGameScore.log', $response);

    return json_decode($response, true);
}

function curlRequest($url, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification if necessary
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL verification if necessary

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        file_put_contents('setGameScore.log', "CURL error: " . $error_msg . "\n", FILE_APPEND);
    }
    curl_close($ch);

    return $response;
}


function logError($message, $result, $userId, $chatId, $score, $messageId) {
    $log = [
        'message' => $message,
        'result' => $result,
        'user_id' => $userId,
        'chat_id' => $chatId,
        'score' => $score,
        'message_id' => $messageId,
        'timestamp' => date("Y-m-d H:i:s")
    ];
    file_put_contents('error.log', json_encode($log) . "\n", FILE_APPEND);
}


?>


