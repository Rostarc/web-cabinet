<?php
// /var/www/html/inc/monitoring.php
echo "<h2>Мониторинг (Ping)</h2>";

if (isset($_POST['ping_ip'])) {
    $ip = escapeshellarg($_POST['ping_ip']); // Простая защита
    $output = shell_exec("ping -c 4 $ip 2>&1");
    echo "<pre>$output</pre>";
}

?>
<form method="post">
    <label>IP/Host для пинга:</label>
    <input type="text" name="ping_ip" placeholder="8.8.8.8">
    <button type="submit">Ping</button>
</form>
