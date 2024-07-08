<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Listas de Contatos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
                float: left;
                display: block;
                color: #f2f2f2;
                background: #28a745;
                border-radius: 5px;
                margin-left: 10px;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
                font-size: 17px;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar a.active {
            background-color: #04AA6D;
            color: white;
        }
        .navbar .icon {
            display: none;
        }
        .container {
            padding: 20px;
        }
        .content {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        input[type="text"], textarea, select, input[type="file"], input[type="number"] {
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
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        .success {
            display: none;
            padding: 20px;
            background-color: #28a745;
            color: #fff;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }
        .progress {
            display: none;
            width: 100%;
            background-color: #f4f4f4;
            border-radius: 4px;
            margin-bottom: 20px;
            overflow: hidden;
            position: relative;
        }
        .progress-bar {
            height: 20px;
            background-color: #28a745;
            width: 0;
            transition: width 0.4s ease;
        }
        .progress-text {
            text-align: center;
            position: absolute;
            width: 100%;
            color: #fff;
            line-height: 20px;
        }
        @media screen and (max-width: 600px) {
            .navbar a:not(:first-child) {
                display: none;
            }
            .navbar a.icon {
                float: right;
                display: block;
            }
        }
        @media screen and (max-width: 600px) {
            .navbar.responsive {position: relative;}
            .navbar.responsive .icon {
                position: absolute;
                right: 0;
                top: 0;
            }
            .navbar.responsive a {
                float: none;
                display: block;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="navbar" id="myNavbar">
        <a href="index.php">Enviar Mensagens</a>
        </a>
    </div>

    <div class="container">
        <div class="content">
            <h2>Gerenciar Listas de Contatos</h2>
            <form id="listForm">
                <label for="listName">Nome da Lista:</label>
                <input type="text" id="listName" name="listName" required>
                
                <label for="contacts">Números de Contatos (separados por vírgula):</label>
                <textarea id="contacts" name="contacts" rows="4" required></textarea>

                <button type="submit">Salvar Lista</button>
                <div class="success" id="successMessage">Lista salva com sucesso!</div>
            </form>
        </div>
        <div class="content">
            <h2>Listas de Contatos Salvas</h2>
            <ul id="savedLists">
                <!-- As listas serão carregadas aqui -->
            </ul>
        </div>
    </div>

    <script>
        document.getElementById('listForm').addEventListener('submit', function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();

            xhr.open('POST', 'save_list.php', true);

            xhr.onload = function () {
                if (xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        document.getElementById('successMessage').style.display = 'block';
                        setTimeout(() => {
                            document.getElementById('successMessage').style.display = 'none';
                        }, 3000);
                        loadLists();
                    } else {
                        alert(response.message);
                    }
                } else {
                    console.error('Erro:', xhr.responseText);
                }
            };

            xhr.onerror = function () {
                console.error('Erro na conexão.');
            };

            xhr.send(formData);
        });

        function loadLists() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_lists.php', true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    var lists = JSON.parse(xhr.responseText);
                    var savedLists = document.getElementById('savedLists');
                    savedLists.innerHTML = '';
                    lists.forEach(function (list) {
                        var li = document.createElement('li');
                        li.innerHTML = list.name + ' - <a href="edit_list.php?list=' + list.name + '">Editar</a> - <a href="delete_list.php?list=' + list.name + '" onclick="return confirm(\'Tem certeza que deseja excluir esta lista?\');">Excluir</a>';
                        savedLists.appendChild(li);
                    });
                }
            };
            xhr.send();
        }

        function myFunction() {
            var x = document.getElementById("myNavbar");
            if (x.className === "navbar") {
                x.className += " responsive";
            } else {
                x.className = "navbar";
            }
        }

        document.addEventListener('DOMContentLoaded', loadLists);
    </script>
</body>
</html>
