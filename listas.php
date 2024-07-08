<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listas de Contatos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #218838;
        }
        .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .list-item button {
            background-color: #dc3545;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Listas de Contatos</h2>
        <form id="listForm">
            <label for="listName">Nome da Lista:</label>
            <input type="text" id="listName" name="listName" required>
            
            <label for="numbers">Números (separados por vírgula):</label>
            <textarea id="numbers" name="numbers" rows="4" required></textarea>
            
            <button type="submit">Salvar Lista</button>
        </form>
        <h3>Listas Salvas:</h3>
        <div id="savedLists"></div>
    </div>
    
    <script>
        document.getElementById('listForm').addEventListener('submit', function (event) {
            event.preventDefault();
            var listName = document.getElementById('listName').value;
            var numbers = document.getElementById('numbers').value;
            saveList(listName, numbers);
        });

        function saveList(listName, numbers) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_list.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function () {
                if (xhr.status == 200) {
                    loadLists();
                } else {
                    console.error('Erro ao salvar lista:', xhr.responseText);
                }
            };
            xhr.send(JSON.stringify({ listName: listName, numbers: numbers }));
        }

        function loadLists() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_lists.php', true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    var lists = JSON.parse(xhr.responseText);
                    var savedLists = document.getElementById('savedLists');
                    savedLists.innerHTML = '';
                    lists.forEach(function (list) {
                        var listItem = document.createElement('div');
                        listItem.classList.add('list-item');
                        listItem.innerHTML = `
                            <span>${list.name}</span>
                            <div>
                                <button onclick="editList('${list.name}')">Editar</button>
                                <button onclick="deleteList('${list.name}')">Excluir</button>
                            </div>
                        `;
                        savedLists.appendChild(listItem);
                    });
                } else {
                    console.error('Erro ao carregar listas:', xhr.responseText);
                }
            };
            xhr.send();
        }

        function editList(listName) {
            var numbers = prompt('Digite os números (separados por vírgula):');
            if (numbers !== null) {
                saveList(listName, numbers);
            }
        }

        function deleteList(listName) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_list.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function () {
                if (xhr.status == 200) {
                    loadLists();
                } else {
                    console.error('Erro ao excluir lista:', xhr.responseText);
                }
            };
            xhr.send(JSON.stringify({ listName: listName }));
        }

        // Carregar as listas ao carregar a página
        document.addEventListener('DOMContentLoaded', loadLists);
    </script>
</body>
</html>
