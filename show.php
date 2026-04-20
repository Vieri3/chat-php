<?php
// show.php — Получение всех сообщений
// Вызывается клиентом каждые 1.5 секунды

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$filename = 'messages.json';

if (!file_exists($filename)) {
    echo json_encode([]);
    exit;
}

$content = file_get_contents($filename);
$messages = json_decode($content, true) ?: [];

echo json_encode($messages, JSON_UNESCAPED_UNICODE);
