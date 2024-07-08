<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $listName = $_POST['listName'];
    $contacts = $_POST['contacts'];
    $contactsArray = array_map('trim', explode(',', $contacts));
    $filePath = "lists/$listName.json";

    if (file_put_contents($filePath, json_encode($contactsArray))) {
        echo "Lista salva com sucesso.";
    } else {
        echo "Erro ao salvar a lista.";
    }
}
?>
