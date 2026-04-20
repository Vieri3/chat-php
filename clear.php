<?php
// clear.php — Очистка сообщений 

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$filename = 'messages.json';
$folder_path = 'temp';
$files = glob($folder_path . '/*');

if (file_exists($filename)) {
    // delete messages
    unlink($filename);
    // delete files
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    header('Location: chat.php');
    exit;
} else {
    header('Location: index.php');
    exit;
}
