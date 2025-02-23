<?php
// /var/www/html/scripts/ajax_sysstats.php
header('Content-Type: application/json');

$log_file = '/var/log/sys_stats.log';
$data = array();

if (file_exists($log_file)) {
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Каждая строка имеет формат: timestamp cpu_usage ram_usage disk_perc
        $parts = preg_split('/\s+/', $line);
        if (count($parts) >= 4) {
            $data[] = array(
                'time' => (int)$parts[0],
                'cpu'  => (float)$parts[1],
                'ram'  => (float)$parts[2],
                'diskPerc' => (int)$parts[3]
            );
        }
    }
}

echo json_encode($data);
?>
