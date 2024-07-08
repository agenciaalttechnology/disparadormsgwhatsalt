<?php
if (isset($_GET['name'])) {
    $listName = $_GET['name'];
    $filePath = "lists/$listName.json";
    if (file_exists($filePath)) {
        $list = json_decode(file_get_contents($filePath), true);
        echo json_encode($list);
    }
}
?>
