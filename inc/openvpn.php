<?php
// /var/www/html/inc/openvpn.php

echo "<h2>Управление OpenVPN</h2>";

// Отображаем форму для загрузки .ovpn
?>
<div class="container">
    <form method="post" enctype="multipart/form-data" action="/scripts/upload_openvpn.php">
        <label for="openvpn_file">Выберите файл (.ovpn):</label>
        <input type="file" id="openvpn_file" name="config_file" accept=".ovpn">
        <button type="submit" class="green-button">Загрузить и применить</button>
    </form>
</div>

<?php
// Информация об активном конфиге:
$openvpn_config_file = '/etc/openvpn/client1.conf';
if (file_exists($openvpn_config_file)) {
    $file_content = file_get_contents($openvpn_config_file);
    if (preg_match('/remote\s+(\d+\.\d+\.\d+\.\d+)/', $file_content, $m)) {
        $ip_address = $m[1];
        echo "<p>Активный конфиг: client1.conf (IP: $ip_address)</p>";
    } else {
        echo "<p>Активный конфиг: client1.conf (IP не найден)</p>";
    }
} else {
    echo "<p>Конфиг OpenVPN не найден</p>";
}
