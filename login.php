<?php
session_start();

// Если уже авторизованы — перенаправляем на главную
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $htpasswd_file = '/etc/apache2/.htpasswd';
    if (file_exists($htpasswd_file)) {
        $lines = file($htpasswd_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $found = false;
        foreach ($lines as $line) {
            // Формат: login:hash
            list($user, $hash) = explode(":", $line, 2);
            if ($user === $username) {
                $found = true;
                // Используем хэш как соль для crypt()
                if (crypt($password, $hash) === $hash) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['username'] = $username;
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Неверный пароль";
                }
            }
        }
        if (!$found) {
            $error = "Пользователь не найден";
        }
    } else {
        $error = "Файл авторизации не найден";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
</head>
<body>
    <h1>Вход на сайт</h1>
    <?php if ($error): ?>
        <div style="color: red;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <label>Логин: <input type="text" name="username" required></label><br>
        <label>Пароль: <input type="password" name="password" required></label><br>
        <button type="submit">Войти</button>
    </form>
</body>
</html>
