<?php
// /var/www/html/scripts/ajax_ping_custom.php
header('Content-Type: application/json');

$host = isset($_GET['host']) ? $_GET['host'] : 'google.com';

// Выполняем ping (1 пакет)
$cmd = sprintf('ping -c 1 %s 2>&1', escapeshellarg($host));
$output = shell_exec($cmd);

$pingTime = -1;
$line = "[Ping error]";
if (preg_match('/time=(\d+(\.\d+)?)/', $output, $m)) {
    $pingTime = floatval($m[1]);
    // Приведём строку вывода к более короткой
    $line = "Reply from $host: time={$pingTime} ms";
} else {
    // можем вывести полный $output для отладки
    $line = $output;
}

// Возвращаем JSON
echo json_encode([
  'ping'   => $pingTime,
  'output' => $line,
]);
