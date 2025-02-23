<?php
// Получаем параметр диапазона времени
header('Content-Type: application/json');

// Принимаем GET-параметр range (время в секундах). Например, range=3600 — за 1 час
$range = isset($_GET['range']) ? (int)$_GET['range'] : 300;  // по умолчанию — последние 5 минут
$log_file = '/var/log/ping_history.log';

$data = [];
$cutoff = time() - $range;  // удаляем старые данные, если их время меньше, чем cutoff

// Читаем лог и фильтруем по времени
if (file_exists($log_file)) {
    $lines = shell_exec("tail -n 2000 " . escapeshellarg($log_file));
    foreach (explode("\n", trim($lines)) as $line) {
        if (preg_match('/^(\d+)\s+([\d\.]+)/', $line, $matches)) {
            $ts = (int)$matches[1];
            $ping = (float)$matches[2];
            if ($ts >= $cutoff) {
                $data[] = ['time' => $ts, 'ping' => $ping];
            }
        }
    }
}

echo json_encode($data);
