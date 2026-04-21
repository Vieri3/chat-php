<?php
session_start();

// Если сессия уже установлена, сразу перенаправляем на chat.php
if (isset($_SESSION['access_granted']) && $_SESSION['access_granted'] === true) {
    header('Location: chat.php');
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = $_POST['code'] ?? '';
    // Здесь можно заменить на свою логику проверки (например, из БД)
    if ($entered_code === '32') {
        $_SESSION['access_granted'] = true;
        $_SESSION["name"] = 'Admin';
        header('Location: chat.php');
        exit;
    } else if ($entered_code === '0524') {
        $_SESSION['access_granted'] = true;
        $_SESSION["name"] = 'User';
        header('Location: chat.php');
        exit;
    } else {
        $error = 'Неверный код!';
    }
}
?>

<?php require 'header.php' ?>

<style>
    body {
        background: #000000;
        color: #00ff00;
        width: 100%;
        height: 100%;
        /* Запрещает скролл страницы */
        overflow: hidden;
        /* Фиксирует страницу, предотвращая сдвиг при открытии клавиатуры */
        position: fixed;
        /* Отключает обработку жестов браузером (панорамирование/зум) */
        touch-action: none;
    }

    form {
        margin-top: 10px;
        font-size: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    input {
        font-size: 24px;
        outline: none;
        border: none;
        border-bottom: 1px dotted #00ff00;
        background-image: none;
        background-color: transparent;
        box-shadow: none;
        color: #00ff00;
        margin-left: 10px;
    }
    
    button {
        padding: 10px 20px;
        background: #000000;
        border-radius: 5px;
        border: 1px dotted #00ff00;
        cursor: pointer;
        font-size: 14px;
        color: #00ff00;
    }

    button:hover {
        background: rgb(64, 64, 64);
    }
</style>

<body>
    <form method="post">
        <div><b>>:</b><input name="code" type="text" autocomplete="off" required /></div>
        <button type="submit">Execute</button>
        <?php if (isset($error)) echo "<div style='color: red;'>$error</div>"; ?>
    </form>
</body>

</html>