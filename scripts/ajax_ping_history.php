<?php
// Возвращает последние ~60 строк /var/log/ping_history.log
header('Content-Type: application/json');
$lines = shell_exec('tail -n 60 /var/log/ping_history.log 2>&1');
$data = [];
foreach (explode("\n", trim($lines)) as $line) {
    // ожидаем формат "timestamp pingValue"
    if (preg_match('/^(\d+)\s+([\d\.]+)/', $line, $m)) {
        $data[] = [
            'time' => (int)$m[1],
            'ping' => (float)$m[2],
        ];
    }
}
echo json_encode($data);
