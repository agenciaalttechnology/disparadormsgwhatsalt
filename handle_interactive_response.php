<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $apiKey = "rgzgais2ebe3tzxagjujfo"; // Substitua pela sua chave de API
    $estancia = "alefe"; // Substitua pela sua estância
    $urlapi = "https://evolutionapi.alttechnology.com.br/";

    $input = json_decode(file_get_contents("php://input"), true);
    $buttonId = $input['interactive']['button_reply']['id'];
    $confirmationNumber = $input['from'];

    $message = "";

    if ($buttonId == "success_list") {
        $message = "📋 *Lista de Sucessos:*\n" . implode("\n", $_SESSION['successNumbers']);
    } elseif ($buttonId == "failure_list") {
        $message = "📋 *Lista de Falhas:*\n" . implode("\n", $_SESSION['failedNumbers']);
    }

    $payload = [
        "number" => $confirmationNumber,
        "textMessage" => ["text" => $message]
    ];
    $url = $urlapi . "message/sendText/" . $estancia;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'apikey: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    curl_close($ch);
}
