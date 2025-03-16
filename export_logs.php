<?php
// export_logs.php

// Функция для чтения, переворачивания строк и фильтрации (возвращает массив строк)
function get_log_lines($filePath, $linesCount = 50) {
    if (!file_exists($filePath)) {
        return [];
    }
    $logContent = shell_exec("sudo tail -n $linesCount " . escapeshellarg($filePath));
    $lines = explode("\n", trim($logContent));
    return array_reverse($lines); // свежие записи первыми
}

// Собираем логи
$logs = [];

// 1. Логи входов (last)
$rawLogins = shell_exec('last -n 20');
$loginLines = explode("\n", trim($rawLogins));
$loginLines = array_reverse($loginLines);
$logs[] = "=== Последние входы в систему ===";
$logs = array_merge($logs, $loginLines);

// 2. DHCP лог
$rawDhcp = shell_exec("sudo systemctl status isc-dhcp-server --no-pager 2>&1");
$logs[] = "\n=== DHCP Лог ===";
$logs[] = $rawDhcp;

// 3. OpenVPN лог
$ovpnLog = shell_exec("sudo journalctl -u openvpn@client1 --no-pager -n 30 2>&1");
$logs[] = "\n=== OpenVPN Лог (последние 30 строк) ===";
$logs[] = $ovpnLog;

// 4. WireGuard лог
$wgLog = shell_exec("sudo journalctl -u wg-quick@tun0 --no-pager -n 30 2>&1");
$logs[] = "\n=== WireGuard Лог (последние 30 строк) ===";
$logs[] = $wgLog;

// 5. Системные показатели (sys_stats)
$sysStatsPath = '/var/log/sys_stats.log';
$sysStatsLines = [];
if (file_exists($sysStatsPath)) {
    $lines = file($sysStatsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_slice($lines, -50);
    $lines = array_reverse($lines);
    // Фильтруем только те записи, где хотя бы один параметр больше 49%
    foreach ($lines as $line) {
        $parts = preg_split('/\s+/', $line);
        if (count($parts) >= 4) {
            $cpu = floatval($parts[1]);
            $ram = floatval($parts[2]);
            $disk = floatval($parts[3]);
            if ($cpu > 49 || $ram > 49 || $disk > 49) {
                $sysStatsLines[] = $line;
            }
        }
    }
}
$sysStatsLines = array_unique($sysStatsLines);
$sysStatsLines = array_slice($sysStatsLines, 0, 25);
$logs[] = "\n=== Системные показатели (записи, где хотя бы один параметр > 50%) ===";
$logs = array_merge($logs, $sysStatsLines);

// 6. Системный журнал (syslog)
$syslogLines = get_log_lines('/var/log/syslog', 50);
$logs[] = "\n=== Системный журнал (syslog, последние 50 строк) ===";
$logs = array_merge($logs, $syslogLines);

// 7. Лог веб-сервера (Apache Error Log)
$apacheLogPath = '/var/log/apache2/error.log';
$apacheLines = get_log_lines($apacheLogPath, 50);
$logs[] = "\n=== Лог веб-сервера (Apache Error Log) ===";
$logs = array_merge($logs, $apacheLines);

// Фильтруем итоговый массив, убираем пустые строки и дубликаты
$logs = array_filter($logs, function($line) {
    return trim($line) !== "";
});
$logs = array_unique($logs);

// Ограничиваем итоговый лог, если он слишком большой (например, оставляем не более 200 строк)
$logs = array_slice($logs, 0, 200);

// Объединяем все строки в один текстовый документ
$finalLog = implode("\n", $logs);

// Отправляем заголовки для загрузки файла
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="logs_export.txt"');
echo $finalLog;
exit;
?>
