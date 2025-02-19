<?php
// /var/www/html/inc/wireguard.php

echo "<h2>Управление WireGuard</h2>";
?>
<div class="container">
    <form method="post" enctype="multipart/form-data" action="/scripts/upload_wireguard.php">
        <label for="wireguard_file">Выберите файл (.conf):</label>
        <input type="file" id="wireguard_file" name="config_file" accept=".conf">
        <button type="submit" class="green-button">Загрузить и применить</button>
    </form>
</div>

<?php
// Информация о конфиге WireGuard:
$config_file = '/etc/wireguard/tun0.conf';
if (file_exists($config_file)) {
    $content = file_get_contents($config_file);
    if (preg_match('/Endpoint\s*=\s*([^\s]+)/', $content, $matches)) {
        $ip_address = $matches[1];
        echo "<p>Активный конфиг: tun0.conf (Endpoint: $ip_address)</p>";
    } else {
        echo "<p>Активный конфиг: tun0.conf (Endpoint не найден)</p>";
    }
} else {
    echo "<p>Конфиг WireGuard не найден</p>";
}
?>
