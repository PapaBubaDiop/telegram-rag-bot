<?php
    include '../diop.php';  // Adjust the path as needed if your config.php is outside the public directory
    $botToken = DB_TOKEN_HOBO;
    $apiUrl = "https://api.telegram.org/bot$botToken/";
    $user = isset($_GET['id']) ? (int)$_GET['id'] : 1967;
    $content = file_get_contents("php://input");
    
    // 2. Save it to a file for inspection
// file_put_contents(__DIR__ . '/last_update.json', $content);

    
    $debugJson = '{
    "update_id": 123456,
    "message": {
        "message_id": 10,
        "from": {"id": 8524593380, "first_name": "Badimus"},
        "chat": {"id": 8524593380, "type": "private"},
        "text": "Hello debug!"
    }
}';

// $content = $debugJson;              // simulate Telegram JSON

   $update = json_decode($content, true);

    if (!$update) {
    // Received data is not valid
      echo "oops<br>";
        exit;
    }
    

    if (isset($update['message'])) {
        handleIncomingMessage($update['message']);
    } elseif (isset($update['callback_query'])) {
        handleCallbackQuery($update['callback_query']);
    }

// Function to send best scorers
function sendBestScorers($uid) {
    global $apiUrl;
    $text = "🏆️ Today Best\n\n";
    $index = 0;
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = $mysql->query("SELECT * FROM bashni ORDER BY score ASC LIMIT 5");
    if (!$result) {
        die("Query failed: " . $mysql->error);
    }
    $me = 0;
    while ($row = $result->fetch_assoc()) {
        $index  = $index + 1;
        $name=$row['name'];
        $pts=$row['score'];
        if ($row['userid'] == $uid) {
            $text = $text. " ". $index . ". " . $name . " - " . $pts . "\n";
            $me = $index;
        } else {
            $text = $text. " ". $index . ". " . $name . " - " . $pts . "\n";
        }
    }
    if ($me == 0) {
        $result = $mysql->query("SELECT * FROM bashni WHERE userid='$uid' LIMIT 1");
        if ($row = $result->fetch_assoc()) {
            $s=$row['score'];
            $text = $text. " ". $index . ". " . $name . " - " . $s . "\n";
        }    
    }
    $mysql->close();
    return $text;
}





// Function to send game links
function sendGameLinks($chatId, $uid, $text) {
    global $apiUrl;
    
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '🧩 Play 2 Bashni',  'callback_data' => '1', 'game_short_name' => 'bashni'],
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


/*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . "sendMessage");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($params))
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo "Curl error: $error_msg";
    }

    curl_close($ch);
    return $response;

}
*/





function saveWalletAddress($uid, $address) {
    global $apiUrl;
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysql->query("UPDATE bashni SET wallet = '$address' WHERE userid='$uid'");
    $mysql->close();
}



function saveUserToDb(array $from)
{
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysql->set_charset('utf8mb4');

    $userId = (int)$from['id'];
    $name   = $mysql->real_escape_string($from['first_name'] ?? '');
    $last   = $mysql->real_escape_string($from['last_name']  ?? '');
    $user   = $mysql->real_escape_string($from['username']   ?? '');

    $result = $mysql->query("SELECT * FROM bashni WHERE userid = $userId LIMIT 1");

    if ($row = $result->fetch_assoc()) {
        $mysql->query("
            UPDATE bashni
            SET name = '$name', surname = '$last'
            WHERE userid = $userId
        ");
    } else {
        $mysql->query("
            INSERT INTO bashni (userid, name, surname, nameid)
            VALUES ('$userId', '$name', '$last', '$user')
        ");
    }

    $mysql->close();
}




function handleIncomingMessage($message) {
    global $apiUrl;

    $messageId = (int)$message['message_id'];
    $chatId = (int)$message['chat']['id'];
    $text = $message['text'];


    saveUserToDb($message['from']);
/*
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysql->set_charset('utf8mb4');
    $userId = (int)$message['from']['id'];          
    $name   = $message['from']['first_name'] ?? '';
    $last   = $message['from']['last_name']  ?? '';
    $user   = $message['from']['username']   ?? '';

    $result = $mysql->query("SELECT * FROM bashni WHERE userid='$userId' LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $mysql->query("UPDATE bashni SET name = '$name', surname = '$last'  WHERE userid='$userId'");
    } else {
        $mysql->query("INSERT INTO bashni (userid, name, surname, nameid) VALUES ('$userId', '$name', '$last', '$user')");
    }
    $mysql->close();
  */
  
    sendGameLinks($chatId, $userId, sendBestScorers($userId));
}


function handleCallbackQuery($callbackQuery) {
    global $apiUrl;
    $callbackQueryId = $callbackQuery['id'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $userId = $callbackQuery['from']['id'];
    
    $userFrom  = $callbackQuery['from'];
    saveUserToDb($userFrom);

    
    $data = $callbackQuery['data'];
    switch ($data) {
        case '1':
            sendGame($chatId, $data);
            break;
        case '2':
            sendGame($chatId, $data);
            break;
        default:
            answerCallbackQuery($callbackQueryId, $userId);
            break;
    }
}


function validateWalletAddress($address) {
    // TON wallet addresses are typically 48 characters long in base64url format
    return preg_match('/^[A-Za-z0-9_-]{48}$/', $address);
}



  


function sendMessage($chatId, $text) {
    global $apiUrl;

    $params = [
        'chat_id' => $chatId,
        'text' => $text
    ];

    file_get_contents($apiUrl . "sendMessage?" . http_build_query($params));
}

function sendGame($chatId, $data) {
    global $apiUrl;
    $gameShortName = 'bashni';
    $params = [
        'chat_id' => $chatId,
        'game_short_name' => $gameShortName,
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Play', 'callback_game' => json_encode(new stdClass())]]]])
    ];
    file_get_contents($apiUrl . "sendGame?" . http_build_query($params));
}


 
function answerCallbackQuery($callbackQueryId, $userId) {
    global $apiUrl;
    
    $r = random_int(1, 1000);

    $params = [
        'callback_query_id' => $callbackQueryId,
        'show_alert' => false,
        'url' => 'https://bashurov.net/bashni/?id='.$userId.'&r='.$r
    ];
    
  

    file_get_contents($apiUrl . "answerCallbackQuery?" . http_build_query($params));
}





?>


