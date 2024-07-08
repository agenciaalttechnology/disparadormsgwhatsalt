<?php
if (isset($_GET['list'])) {
    $listName = $_GET['list'];
    $filePath = "lists/$listName.json";
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    header("Location: contact_list.php");
}
?>
