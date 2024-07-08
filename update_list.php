<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $originalName = $_POST['originalName'];
    $listName = $_POST['listName'];
    $numbers = explode(',', $_POST['contacts']);

    $filePath = 'lists.json';
    $lists = json_decode(file_get_contents($filePath), true);

    foreach ($lists as &$list) {
        if ($list['name'] === $originalName) {
            $list['name'] = $listName;
            $list['numbers'] = $numbers;
            break;
        }
    }

    file_put_contents($filePath, json_encode($lists));

    echo "Lista atualizada com sucesso!";
}
?>
