<?php
// Certifique-se de que o nome da lista está sendo passado corretamente
$listName = isset($_GET['list']) ? $_GET['list'] : '';

// Carregar números da lista selecionada
$contacts = '';
if ($listName) {
    $filePath = "lists/$listName.json";
    if (file_exists($filePath)) {
        $numbers = json_decode(file_get_contents($filePath), true);
        if ($numbers) {
            $contacts = implode(',', $numbers);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Lista de Contatos</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            padding: 15px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        .back-button {
            background-color: #007bff;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Lista de Contatos</h2>
        <form action="save_list.php" method="POST">
            <label for="listName">Nome da Lista:</label>
            <input type="text" id="listName" name="listName" value="<?php echo htmlspecialchars($listName); ?>" readonly>

            <label for="contacts">Números de Contatos (separados por vírgula):</label>
            <textarea id="contacts" name="contacts" rows="10"><?php echo htmlspecialchars($contacts); ?></textarea>

            <button type="submit">Salvar Alterações</button>
            <button type="button" class="back-button" onclick="window.location.href='contact_list.php'">Voltar</button>
        </form>
    </div>
</body>
</html>
