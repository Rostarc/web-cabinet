<?php
// Файл: check_for_update.php

// Путь к файлу с текущей версией на сервере
$version_file = '/var/www/html/vpn-web-installer/version.txt';
$repo_version_url = 'https://raw.githubusercontent.com/Rostarc/web-cabinet/main/version.txt'; // Новый путь к файлу версии

// Получаем текущую версию с репозитория
$remote_version = file_get_contents($repo_version_url);

// Проверяем текущую локальную версию
$local_version = file_get_contents($version_file);

if ($remote_version != $local_version) {
    echo json_encode(['update_available' => true, 'remote_version' => $remote_version, 'local_version' => $local_version]);
} else {
    echo json_encode(['update_available' => false]);
}
?>
