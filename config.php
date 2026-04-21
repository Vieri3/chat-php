<?php

$title_chat = '💬 PHP - chat';

$logo_chat = '💬 PHP';

// при изменениях стилей чтобы избавиться от кеширования броаузера
$versus_style = '?v1.0.2';

// Формирование ссылки для SendFile() 
$isDev = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

if ($isDev) {
    // для development
    $baseLink = "https://" . $_SERVER['SERVER_NAME'] . "/temp/";
} else {
    // для productiom
    $baseLink = "https://" . $_SERVER['SERVER_NAME'] . "/vieri/chat-php/temp/";
}
