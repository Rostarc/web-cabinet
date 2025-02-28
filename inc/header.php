<?php
// /var/www/html/inc/header.php
$publicIp = trim(shell_exec('curl -s ifconfig.me'));
?>
<div style="
  position:absolute;
  top:22px;
  left:50%;
  transform:translateX(-50%);
  background:#333;
  padding:5px 10px;
  border-radius:4px;">
    <span>IP: <?php echo htmlspecialchars($publicIp); ?></span>
</div>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>VPN Panel</title>
    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<header>
    <div class="header-logo">
        <a href="/"><img src="/logo.png" alt="Logo" height="40"></a>
        <span class="header-title">VPN Panel</span>
    </div>
    <!-- Кнопка для выхода -->
    <div style="position: absolute; top: 22px; right: 20px;">
        <a href="logout.php" style="color: #fff; text-decoration: none;">Выйти</a>
    </div>
    <!-- Глобальный элемент для пинг-статуса -->
    <div id="ping-status" style="position: absolute; top: 22px; right: 220px; width: 200px; background-color: #333; padding: 8px; border-radius: 6px; text-align: center;">
        Проверка пинга...
    </div>
</header>

<!-- Подключаем скрипт для обновления глобального пинга -->
<script src="/js/global_ping.js"></script>
