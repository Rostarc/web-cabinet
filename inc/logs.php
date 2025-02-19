<?php
// /var/www/html/inc/logs.php
echo "<h2>Просмотр логов</h2>";

$logFile = '/var/log/vpn-web.log';
if (file_exists($logFile)) {
    // Покажем последние 50 строк
    $output = shell_exec("sudo tail -n 50 $logFile 2>&1");
    echo "<pre style='background:#333; color:#0f0; padding:10px;'>";
    echo htmlspecialchars($output);
    echo "</pre>";
} else {
    echo "<p>Лог-файл не найден.</p>";
}
