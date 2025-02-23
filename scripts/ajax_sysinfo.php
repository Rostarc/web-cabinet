<?php
// /var/www/html/scripts/ajax_sysinfo.php
header('Content-Type: application/json');

// CPU (простой способ: top -b -n1 | grep "Cpu(s)")
$cpuLine = shell_exec("top -b -n1 | grep 'Cpu(s)'");
$cpuUsage = 0;
if (preg_match('/(\d+\.\d+)\s+us,/', $cpuLine, $m)) {
    $cpuUsage = floatval($m[1]); // Это user space usage, можно суммировать user+system
}

// RAM
$free = shell_exec("free -m");
$lines = explode("\n", trim($free));
$memLine = isset($lines[1]) ? $lines[1] : ""; // вторая строка "Mem:"
$ramUsage = 0;
$ramTotal = 0;
if ($memLine) {
    $parts = preg_split('/\s+/', $memLine);
    // free выводит: total, used, free, shared, buff/cache, available
    $ramTotal = (int)$parts[1];
    $ramUsed  = (int)$parts[2];
    $ramUsage = round(($ramUsed / $ramTotal)*100, 1);
}

// Disk
// Парсим df -h /
$df = shell_exec("df -h / | tail -1");
$parts = preg_split('/\s+/', trim($df));
$diskTotal = $parts[1] ?? '??';
$diskUsed  = $parts[2] ?? '??';
// $parts[4] будет процент

echo json_encode([
    'cpu'       => $cpuUsage,
    'ram'       => $ramUsage,
    'ramTotal'  => $ramTotal,
    'diskUsed'  => $diskUsed,
    'diskTotal' => $diskTotal
]);
