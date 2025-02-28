<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, "/");
    session_start();
}
// Ограничение доступа (например, только для локальной сети, если нужно)
if (!preg_match('/^192\.168\./', $_SERVER['REMOTE_ADDR'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Доступ запрещен");
}
// Проверка авторизации
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /login.php");
    exit;
}
?>
