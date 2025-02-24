<?php
session_start();

// Ограничение доступа только для локальной сети (пример проверки IP)
// Предполагается, что локальные IP начинаются с "192.168."
if (!preg_match('/^192\.168\./', $_SERVER['REMOTE_ADDR'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Доступ запрещен");
}

// Проверка авторизации
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
