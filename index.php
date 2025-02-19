<?php
// /var/www/html/index.php
require_once 'config.php'; // Подключаем конфиг

// В дальнейшем здесь будет проверка авторизации (Basic Auth или своя).
// Пока — без неё.
include 'inc/header.php';      // шапка
include 'inc/navigation.php';  // меню

echo '<div class="main-container">';

// Получаем page=? из URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// В зависимости от page подключаем нужный файл
switch ($page) {
    case 'openvpn':
        include 'inc/openvpn.php';
        break;
    case 'wireguard':
        include 'inc/wireguard.php';
        break;
    case 'logs':
        include 'inc/logs.php';
        break;
    case 'update':
        include 'inc/update.php';
        break;
    case 'console':
        include 'inc/console.php';
        break;
    case 'monitoring':
        include 'inc/monitoring.php';
        break;
    default:
        // страница "по умолчанию" или 404
        echo "<h1>Главная страница</h1>";
        echo "<p>Добро пожаловать в VPN-панель!</p>";
        break;
}

echo '</div>';

include 'inc/footer.php'; // футер
