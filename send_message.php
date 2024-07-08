<?php
ignore_user_abort(true); // Continue script execution even if the user aborts the request
set_time_limit(0); // Remove the time limit for script execution

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $apiKey = "rgzgais2ebe3tzxagjujfo"; // Substitua pela sua chave de API
    $estancia = "alefe"; // Substitua pela sua estância
    $listName = $_POST['listName'];
    $messageType = $_POST['messageType'];
    $message = $_POST['message'];
    $delayMin = 2; // Valor mínimo do atraso em segundos
    $delayMax = 5; // Valor máximo do atraso em segundos
    $confirmationNumbers = ["5533984312397", "5533999909806"]; // Números para enviar confirmação
    $urlapi = "https://evolutionapi.alttechnology.com.br/";
    $batchSize = 5; // Tamanho do lote

    // Carregar números da lista selecionada
    $numbers = [];
    if ($listName) {
        $filePath = "lists/$listName.json";
        if (file_exists($filePath)) {
            $numbers = json_decode(file_get_contents($filePath));
        }
    }

    // Limpar e validar números de telefone
    $validNumbers = [];
    foreach ($numbers as $number) {
        $number = preg_replace('/\s+/', '', $number); // Remove todos os espaços em branco
        $number = preg_replace('/\D/', '', $number); // Remove caracteres não numéricos
        if (strlen($number) == 13) { // Verifica se o número tem 13 dígitos (formato internacional)
            $validNumbers[] = $number;
        }
    }

    $results = [];
    $totalNumbers = count($validNumbers);
    $batches = array_chunk($validNumbers, $batchSize);
    $successCount = 0;
    $failureCount = 0;

    foreach ($batches as $batch) {
        foreach ($batch as $number) {
            // Verificar se o botão de parar foi clicado
            if (file_exists("stop_sending.txt")) {
                $results[] = "Envio interrompido pelo usuário.";
                break 2;
            }

            $number = trim($number);
            $payload = [
                "number" => $number,
                "options" => ["delay" => 1200, "presence" => "composing"]
            ];

            if ($messageType == 'text') {
                $payload["textMessage"] = ["text" => $message];
                $url = $urlapi . "message/sendText/" . $estancia;
            } else {
                if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                    $filePath = $_FILES['file']['tmp_name'];
                    $fileData = base64_encode(file_get_contents($filePath));
                    $fileName = $_FILES['file']['name'];

                    $payload["mediaMessage"] = [
                        "mediatype" => $messageType,
                        "caption" => $message,
                        "media" => $fileData
                    ];

                    $url = $urlapi . "message/sendMedia/" . $estancia;
                } else {
                    $results[] = "Falha ao enviar mensagem para $number. Erro: Nenhum arquivo enviado.";
                    $failureCount++;
                    continue;
                }
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'apikey: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                $results[] = "Falha ao enviar mensagem para $number. Erro: " . curl_error($ch);
                $failureCount++;
            } else {
                $results[] = "Mensagem enviada para $number com sucesso. Resposta: $response";
                $successCount++;
            }
            curl_close($ch);

            // Adicionar atraso aleatório entre as mensagens
            sleep(rand($delayMin, $delayMax));
        }
        
        // Adicionar atraso aleatório entre os lotes
        sleep(rand($delayMin, $delayMax));
    }

    // Enviar mensagem de confirmação
    $confirmationMessage = "✅ *Envio Concluído*\n\n"
                         . "📋 *Lista:* $listName\n"
                         . "✅ *Sucessos:* $successCount\n"
                         . "❌ *Falhas:* $failureCount\n\n"
                         . "📅 *Data e Hora:* " . date('d/m/Y H:i:s');

    foreach ($confirmationNumbers as $confirmationNumber) {
        $confirmationPayload = [
            "number" => $confirmationNumber,
            "textMessage" => ["text" => $confirmationMessage]
        ];
        $confirmationUrl = $urlapi . "message/sendText/" . $estancia;
        $ch = curl_init($confirmationUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($confirmationPayload));
        $confirmationResponse = curl_exec($ch);
        if (curl_errno($ch)) {
            $results[] = "Falha ao enviar mensagem de confirmação para $confirmationNumber. Erro: " . curl_error($ch);
        } else {
            $results[] = "Mensagem de confirmação enviada com sucesso para $confirmationNumber. Resposta: $confirmationResponse";
        }
        curl_close($ch);
    }

    foreach ($results as $result) {
        echo "<p>$result</p>";
    }

    // Remover o arquivo de controle quando o envio for concluído
    if (file_exists("stop_sending.txt")) {
        unlink("stop_sending.txt");
    }
}
?>
