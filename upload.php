<?php
// скрипт который загружает в папку /temp на сервере файл отправленный от пользователя
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tempFile = $_FILES['file']['tmp_name'];
    $targetPath = 'temp/' . $_FILES['file']['name'];
    move_uploaded_file($tempFile, $targetPath);
}
