<?php
$apiKey = "xe0b68opv5gm7ydvi05o";
$instance = "zezinho";

// Função para obter a lista de grupos
function getGroups($apiKey, $instance) {
    $url = "https://evolutionapi.alttechnology.com.br/group/list/$instance";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "apikey: $apiKey"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err . "<br>";
        return null;
    } else {
        echo "Group list response: " . $response . "<br>"; // Log da resposta
        return json_decode($response, true);
    }
}

// Função para obter os participantes de um grupo
function getGroupParticipants($apiKey, $instance, $groupJid) {
    $url = "https://evolutionapi.alttechnology.com.br/group/participants/$instance?groupJid=$groupJid";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "apikey: $apiKey"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err . "<br>";
        return null;
    } else {
        echo "Participants response for group $groupJid: " . $response . "<br>"; // Log da resposta
        return json_decode($response, true);
    }
}

// Obter a lista de grupos
$groups = getGroups($apiKey, $instance);

if ($groups && isset($groups['groups'])) {
    // Certificar-se de que o diretório de saída existe
    if (!is_dir('lists')) {
        mkdir('lists', 0777, true);
    }

    foreach ($groups['groups'] as $group) {
        $groupJid = $group['jid'];
        $participants = getGroupParticipants($apiKey, $instance, $groupJid);

        if ($participants && isset($participants['participants'])) {
            $filePath = "lists/group_$groupJid.txt";
            $file = fopen($filePath, "w");

            foreach ($participants['participants'] as $participant) {
                $number = $participant['id'];
                $number = str_replace('@s.whatsapp.net', '', $number);
                fwrite($file, "55$number\n");
            }

            fclose($file);
            echo "Participantes do grupo $groupJid salvos em $filePath.<br>";
        } else {
            echo "Nenhum participante encontrado para o grupo $groupJid.<br>";
        }
    }
} else {
    echo "Nenhum grupo encontrado.<br>";
    echo "Group response: " . json_encode($groups) . "<br>"; // Log da resposta de grupos
}
?>
