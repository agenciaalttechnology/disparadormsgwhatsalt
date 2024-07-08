<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $listName = $_POST['listName'];
    $contacts = explode(',', $_POST['contacts']);

    // Salva a lista no arquivo
    file_put_contents("lists/$listName.json", json_encode($contacts));

    // Retorna uma resposta JSON
    echo json_encode(["status" => "success", "message" => "Lista salva com sucesso!"]);
}
?>
