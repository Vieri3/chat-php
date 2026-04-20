<?php
session_start();

// Проверяем, есть ли доступ
if (!isset($_SESSION['access_granted']) || $_SESSION['access_granted'] !== true) {
    header('Location: index.php');
    exit;
} else {
    $name = $_SESSION["name"];
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, interactive-widget=resizes-content">
    <title>💬 PHP - chat</title>
    <link rel="stylesheet" href="./style.css?v1.0.1">
</head>

<body>
    <div class="chat-container">
        <div class="chat-header">
            <div>💬 PHP</div>

            <!-- кнопка-загрузка(форма) отправка файлов -->
            <form enctype="multipart/form-data">
                <label class="input-file">
                    <input type="file" class="input-file" id="inp-add-file-server">
                    <span>file</span>
                </label>
            </form>
            
            <a href="clear.php" class="link-header">Clear</a>
            <a href="logout.php" class="link-header">Exit</a>
        </div>
        <div class="messages" id="messages"></div>
        <div class="input-area">
            <input type="text" id="message-input" class="message-input" placeholder="Enter a message..." maxlength="500" autocomplete="off">
            <button id="send-btn" class="send-btn">Send</button>
        </div>
    </div>
    <script>
        document.getElementById('message-input').focus();

        // для определения изменений
        let lastMessagesHash = '';

        // name
        let myName = '<?php echo $name ?>';

        // Защита от XSS
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Основная функция загрузки всех сообщений
        function loadMessages() {
            fetch('show.php')
                .then(response => response.json())
                .then(messages => {
                    const currentHash = JSON.stringify(messages);
                    if (currentHash === lastMessagesHash) return; // ничего не изменилось

                    lastMessagesHash = currentHash;
                    renderMessages(messages);
                })
                .catch(err => console.error('Ошибка получения сообщений:', err));
        }

        // Отрисовка сообщений
        function renderMessages(messages) {
            const chat = document.getElementById('messages');
            chat.innerHTML = '';

            if (messages.length === 0) {
                chat.innerHTML = '<div style="text-align:center; color:#999; padding:20px;">Пока нет сообщений. Будьте первым!</div>';
                return;
            }

            messages.forEach(msg => {
                const isMyMessage = msg.name === myName;
                const color = isMyMessage ? 'message-my' : 'message-other';

                const div = document.createElement('div');
                div.className = 'message';

                const MSG = msg.type == 'link' ? `<a href="${msg.fileurl}" class="link-download" download>${msg.filenameupload}</a>` : escapeHtml(msg.text);

                div.innerHTML = `
                    <div class="message-header">
                        <span class="${color}">${escapeHtml(msg.name)}</span>
                        <span class="message-time">${escapeHtml(msg.time)}</span>
                    </div>
                    <div class="message-text ${color}">${MSG}</div>
                `;
                chat.appendChild(div);
            });

            // Прокрутка вниз
            chat.scrollTop = chat.scrollHeight;
        }

        // Отправка сообщения (запускает функцию на сервере) send-msg.php
        async function sendMessage() {
            const messageInput = document.getElementById('message-input').value.trim();

            if (!messageInput) {
                alert('Введите сообщение!');
                return;
            }

            const formData = new FormData();
            formData.append('type', 'txt');
            formData.append('name', myName);
            formData.append('message', messageInput);

            try {
                const response = await fetch('send-msg.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('HTTP ошибка: ' + response.status);
                };

                const result = await response.json();

                if (result.status === 'success') {
                    // Сервер сразу возвращает ВСЕ сообщения
                    lastMessagesHash = JSON.stringify(result.messages);
                    renderMessages(result.messages);
                    document.getElementById('message-input').value = '';
                } else {
                    alert('Ошибка отправки: ' + (result.msg || 'Неизвестная ошибка'));
                }
            } catch (err) {
                console.error('Ошибка отправки:', err);
            }

        }

        // Отправка Файла (запускает функцию на сервере) send-file.php
        async function sendFile() {

            if (!window.FormData) {
                alert("В вашем браузере FormData не поддерживается");
                return;
            }

            const input = this;
            const file = input.files[0];

            if (!file) return;

            // Проверка размера (5 MB)
            if (file.size > 5 * 1024 * 1024) {
                alert("Файл больше 5MB");
                return;
            }

            try {
                const formData = new FormData();
                formData.append('file', file);

                // 1. Загрузка файла
                const uploadResponse = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });

                if (!uploadResponse.ok) {
                    throw new Error('Ошибка загрузки файла');
                }

                // 2. Формирование ссылки
                // const baseLink = "<?php echo "https://" . $_SERVER['SERVER_NAME'] . "/" . "temp/" ?>" || '';
                const baseLink = "<?php echo "https://" . $_SERVER['SERVER_NAME'] . "/vieri/ch/" . "temp/" ?>" || '';
                
                const fileNameUpload = file.name;
                const fileUrl = baseLink + fileNameUpload;

                // const messageLinkOut = `<a href="${fileUrl}" download>${fileNameUpload}</a>`;

                // 3. Отправка сообщения с ссылкой
                const formData2 = new FormData();
                formData2.append('type', 'link');
                formData2.append('name', myName);
                formData2.append('filenameupload', fileNameUpload);
                formData2.append('fileurl', fileUrl);

                const messageResponse = await fetch('send-file.php', {
                    method: 'POST',
                    body: formData2
                });

                if (!messageResponse.ok) {
                    throw new Error('Ошибка отправки сообщения');
                }

                const messageResult = await messageResponse.json();

                if (messageResult.status === 'success') {
                    // Сервер сразу возвращает ВСЕ сообщения
                    lastMessagesHash = JSON.stringify(messageResult.messages);
                    renderMessages(messageResult.messages);
                    document.getElementById('message-input').value = '';
                    console.log("Файл успешно загружен и сообщение отправлено");
                } else {
                    alert('Ошибка отправки: ' + (messageResult.msg || 'Неизвестная ошибка'));
                }
                
            } catch (error) {
                console.error(error);
            }
        };

        // Запуск чата
        function startChat() {
            // Первая загрузка
            loadMessages();

            // Автоматическое обновление каждые 1.5 секунды (короткий polling)
            // Это единственный "таймер", и он на клиенте — на сервере таймеров нет!
            setInterval(loadMessages, 1500);

            // Отправка по кнопке
            document.getElementById('send-btn').addEventListener('click', sendMessage);
            // Отправка по Enter
            document.getElementById('message-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
            // отправка файла 
            document.getElementById('inp-add-file-server').addEventListener('change', sendFile)

            console.log('%cЧат запущен! Отправка сообщения → сервер возвращает все сообщения всем подключённым (через polling).', 'color:#007bff; font-weight:bold');
        }

        // Старт
        window.onload = startChat;
    </script>
</body>

</html>