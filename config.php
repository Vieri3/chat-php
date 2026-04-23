<?php

$title_chat = '💬 PHP - chat';

$logo_chat = '💬 PHP';

// при изменениях стилей чтобы избавиться от кеширования броаузера
$versus_style = '?v1.0.5';

// Формирование ссылки для SendFile() 
$is_dev = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'chat-php');

if ($is_dev) {
    // для development
    $base_link = "https://" . $_SERVER['SERVER_NAME'] . "/";
} else {
    // для productiom
    $base_link = "https://" . $_SERVER['SERVER_NAME'] . "/vieri/chat-php/";
}
