<?php
// send.php — Отправка сообщения
// Запускается при отправке с клиента и возвращает ВСЕ сообщения
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$type = trim($_POST['type'] ?? '');
$name = trim($_POST['name'] ?? '');
$base_link = trim($_POST['base_link'] ?? '');

if (empty($type) || empty($name) || empty($base_link)) {
    echo json_encode(['status' => 'error', 'msg' => 'Не все данные переданы']);
    exit;
}

// Защита от XSS
$type = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

// скрипт который загружает в папку /temp на сервере файл отправленный от пользователя
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // временно загруженный файл на сервер
    $temp_file = $_FILES['file']['tmp_name'];
    $original_name = $_FILES['file']['name'];
    // папка, куда сохраняем
    $target_dir  = 'temp/';                    
    $target_path = $target_dir . $original_name;
    $name_of_download_link = $original_name;

    // Если файл с таким именем уже существует — добавляем (1), (2) и т.д.
    $info = pathinfo($original_name);
    // имя без расширения
    $file_name = $info['filename'];   
    $extension = isset($info['extension']) ? '.' . $info['extension'] : '';

    $counter = 1;
    while (file_exists($target_path)) {
        $target_path = $target_dir .  $file_name . ' (' . $counter . ')' . $extension;
        $name_of_download_link =   $file_name . ' (' . $counter . ')' . $extension;
        $counter++;
    }

    $link_download = $base_link . $target_path;

    // Теперь сохраняем файл
    move_uploaded_file($temp_file, $target_path);
}

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
    'name_of_download_link' => $name_of_download_link,
    'link_download' => $link_download,
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