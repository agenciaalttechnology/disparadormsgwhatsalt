<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mass Message Sender</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
            overflow: auto;
        }
        .navbar {
            width: 100%;
            background-color: #333;
            color: white;
            padding: 10px;
            position: fixed;
            top: 0;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #f2f2f2;
            background: #28a745;
            border-radius: 5px;
            margin-left: 10px;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }
        .logo {
            max-width: 100%;
            max-height: 150px;
            margin: 80px 0 20px;
            display: block;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
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
            padding: 15px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
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
        .success i {
            margin-right: 10px;
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
    </style>
</head>
<body>
    <div class="navbar" id="myNavbar">
        <a href="contact_list.php">Lista de contatos</a>
    </div>
    <img src="https://zezinho.alttechnology.com.br/logo/GOODCOFFEE.png" alt="Logo" class="logo">
    <div class="container">
        <h2>Enviar Mensagens em Massa</h2>
        <form id="messageForm" method="POST" action="send_message.php" enctype="multipart/form-data">
            <label for="listName">Selecione a Lista:</label>
            <select id="listName" name="listName" required>
                <!-- As listas de contatos serão carregadas aqui -->
            </select>

            <label for="messageType">Tipo de Mensagem:</label>
            <select id="messageType" name="messageType" required>
                <option value="text">Enviar Texto</option>
                <option value="image">Enviar Imagem</option>
                <option value="video">Enviar áudio ou vídeo em MP4</option>
            </select>

            <div id="messageField">
                <label for="message">Mensagem:</label>
                <textarea id="message" name="message" rows="4"></textarea>
            </div>

            <div id="fileField" style="display: none;">
                <label for="file">Arquivo:</label>
                <input type="file" id="file" name="file">
            </div>

            <!-- Remove o campo de atraso -->
            <div class="progress" id="progressContainer">
                <div class="progress-bar" id="progressBar"></div>
                <div class="progress-text" id="progressText">0%</div>
            </div>

            <button type="submit">Enviar Mensagem</button>
        </form>
        <div id="successMessage" class="success">
            <i class="fas fa-check-circle"></i>
            Mensagem enviada com sucesso!
        </div>
    </div>

    <script>
        document.getElementById('messageType').addEventListener('change', function () {
            var messageType = this.value;
            var messageField = document.getElementById('messageField');
            var fileField = document.getElementById('fileField');

            if (messageType === 'text') {
                messageField.style.display = 'block';
                fileField.style.display = 'none';
            } else {
                messageField.style.display = 'none';
                fileField.style.display = 'block';
            }
        });

        document.getElementById('messageForm').addEventListener('submit', function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();

            xhr.open('POST', 'send_message.php', true);

            xhr.upload.onprogress = function (event) {
                if (event.lengthComputable) {
                    var percentComplete = (event.loaded / event.total) * 100;
                    document.getElementById('progressContainer').style.display = 'block';
                    document.getElementById('progressBar').style.width = percentComplete + '%';
                    document.getElementById('progressText').textContent = Math.round(percentComplete) + '%';
                }
            };

            xhr.onload = function () {
                if (xhr.status == 200) {
                    document.getElementById('successMessage').style.display = 'block';
                    document.getElementById('messageForm').reset();
                    setTimeout(() => {
                        document.getElementById('successMessage').style.display = 'none';
                    }, 3000);
                } else {
                    console.error('Erro:', xhr.responseText);
                }
                document.getElementById('progressContainer').style.display = 'none';
                document.getElementById('progressBar').style.width = '0%';
                document.getElementById('progressText').textContent = '0%';
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
                    var listNameSelect = document.getElementById('listName');
                    listNameSelect.innerHTML = '';
                    lists.forEach(function (list) {
                        var option = document.createElement('option');
                        option.value = list.name;
                        option.textContent = list.name;
                        listNameSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }

        document.addEventListener('DOMContentLoaded', loadLists);
    </script>
</body>
</html>
