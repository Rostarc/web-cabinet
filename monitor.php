<?php
// Файл: monitor.php
echo "<h2>Мониторинг VPN</h2>";
// Пример команды для получения состояния VPN-соединений
$status = shell_exec('systemctl status openvpn@server');
echo "<pre>$status</pre>";
?>
