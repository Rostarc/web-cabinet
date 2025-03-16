<?php
// api/restart_services.php
session_start();

// Для тестирования (уберите после настройки аутентификации)
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['authenticated'] = true;
}

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("HTTP/1.1 403 Forbidden");
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["status" => "error", "message" => "Доступ запрещён."]);
    exit;
}

$commands = [
    "sudo systemctl daemon-reload",
    "sudo systemctl restart update_metrics.service",
    "sudo systemctl restart network_load.service",
    "sudo systemctl daemon-reload"
];

$results = [];
foreach ($commands as $cmd) {
    // Выполнение команды и сбор вывода
    $output = shell_exec($cmd . " 2>&1");
    $trimmed = trim($output);
    if ($trimmed === "") {
        $trimmed = "Успешно выполнено";
    }
    $results[] = ["command" => $cmd, "output" => $trimmed];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(["status" => "success", "results" => $results]);
exit;
?>
