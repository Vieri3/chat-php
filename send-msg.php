<?php
// send.php — Отправка сообщения
// Запускается при отправке с клиента и возвращает ВСЕ сообщения
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$type = trim($_POST['type'] ?? '');
$name = trim($_POST['name'] ?? '');
$text = trim($_POST['message'] ?? '');

if (empty($name) || empty($text) || empty($type)) {
    echo json_encode(['status' => 'error', 'msg' => 'Имя и сообщение обязательны']);
    exit;
}

// Защита от XSS
$type = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

$filename = 'messages.json';

// Читаем существующие сообщения
$messages = [];
if (file_exists($filename)) {
    $content = file_get_contents($filename);
    $messages = json_decode($content, true) ?: [];
}

// Добавляем новое сообщение
$messages[] = [
    'type' => $type,
    'name' => $name,
    'text' => $text,
    'time' => date('Y-m-d H:i')
];

// Ограничиваем количество сообщений (чтобы файл не разрастался)
if (count($messages) > 100) {
    $messages = array_slice($messages, -50); // оставляем последние 50
}

// Сохраняем с блокировкой файла (чтобы избежать конфликтов при одновременной отправке)
file_put_contents($filename, json_encode($messages, JSON_UNESCAPED_UNICODE), LOCK_EX);

echo json_encode([
    'status' => 'success',
    'messages' => $messages
]);
?>