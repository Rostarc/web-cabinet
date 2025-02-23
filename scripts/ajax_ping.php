<?php
// /var/www/html/scripts/ajax_ping.php
header('Content-Type: application/json');

// Выполняем ping
$output = shell_exec('ping -c 1 google.com 2>&1');

// Ищем "time=12.3 ms"
$pingTime = -1;
if (preg_match('/time=(\d+(\.\d+)?)/', $output, $m)) {
    $pingTime = floatval($m[1]);
}

// Если хотим логировать, что > 100 ms
// (Можно сделать это здесь)
if ($pingTime > 100) {
    // пишем в общий лог
    $log = date("Y-m-d H:i:s")." [WARN] Высокий пинг: {$pingTime} ms\n";
    file_put_contents('/var/log/vpn-web.log', $log, FILE_APPEND);
}

// Возвращаем JSON
echo json_encode([
    'ping' => $pingTime
]);
