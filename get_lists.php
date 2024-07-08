<?php
$lists = [];
$files = scandir('lists');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $listName = pathinfo($file, PATHINFO_FILENAME);
        $lists[] = ["name" => $listName];
    }
}
echo json_encode($lists);
?>
