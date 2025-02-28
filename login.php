<?php
session_set_cookie_params(0, "/");
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    if (empty($username) || empty($password)) {
        $error = "Введите имя пользователя и пароль.";
    } else {
        // Формируем команду для проверки пароля
        // Используем escapeshellarg для защиты от инъекций.
        $command = "echo " . escapeshellarg($password) . " | su - " . escapeshellarg($username) . " -c 'id'";
        exec($command, $output, $return_var);
        
        if ($return_var == 0) {
            // Если команда прошла успешно, сохраняем переменные сессии и перенаправляем пользователя
            $_SESSION["logged_in"] = true;
            $_SESSION["username"] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "Неверное имя пользователя или пароль.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в VPN Panel</title>
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Вход в VPN Panel</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <label for="username">Пользователь:</label>
            <input type="text" name="username" id="username" placeholder="Имя пользователя" required>
            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>
