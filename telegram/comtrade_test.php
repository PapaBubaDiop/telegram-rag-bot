<?php
    include '../diop.php';  // Adjust the path as needed if your config.php is outside the public directory
    $botToken = DB_TOKEN_COMTRADE;
    $aiApiKey = AI_TOKEN_COMTRADE;
    $apiUrl = "https://api.telegram.org/bot$botToken/";
    $user = isset($_GET['id']) ? (int)$_GET['id'] : 1967;
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);
    
    $text = "'Hello'";
    
    echo "prompt => ".$text;
    echo '';
    echo '';

// www.bashurov.net/telegram/comtrade_test.php

$msg = handleAI($text);
echo $msg;
 
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
        ['role' => 'user', 'content' => 'What is the weather like today?'], // Replace with $text or user input
    ],
]));




curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '.$aiApiKey
    ]);


$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, true);

// Extract the assistant's answer
$msg = $response['choices'][0]['message']['content'];
return $msg;
}

?>


