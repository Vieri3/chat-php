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
        $_SESSION["name"] = "Admin";
        header('Location: chat.php');
        exit;
    } else if ($entered_code === '777') {
        $_SESSION['access_granted'] = true;
        $_SESSION["name"] = "User";
        header('Location: chat.php');
        exit;
    } else {
        $error = 'Неверный код!';
    }
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
    <form method="post" class="login-container">
        <div><b>>:</b><input name="code" required class="login-input" type="text" autocomplete="off" /></div>
        <button type="submit">Execute</button>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    </form>
</body>

</html>