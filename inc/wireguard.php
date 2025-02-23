<?php
// /var/www/html/inc/wireguard.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление WireGuard - VPN Panel</title>
    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .header-title-custom {
            text-align: center;
            margin: 20px 0;
            font-size: 1.8em;
        }
        .status {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .console {
            background-color: #000;
            color: #0f0;
            font-family: "Courier New", monospace;
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
        }
        .red-button {
            background-color: #d9534f;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .red-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
<div class="main-container">
    <h2 class="header-title-custom">Управление WireGuard</h2>

    <?php
    // Получаем статус службы WireGuard
    $wireguardStatus = trim(shell_exec('systemctl is-active wg-quick@tun0 2>/dev/null'));
    $statusText = ($wireguardStatus === 'active') ? "Активен" : "Выключен";
    $statusColor = ($wireguardStatus === 'active') ? "lime" : "red";
    ?>
    <div class="status">
        Статус WireGuard: <span style="color: <?php echo $statusColor; ?>;"><?php echo $statusText; ?></span>
    </div>

    <div class="container" style="margin: 20px auto; text-align: center;">
        <form method="post" enctype="multipart/form-data" action="/scripts/upload_wireguard.php">
            <label for="wireguard_file">Выберите файл (.conf):</label>
            <input type="file" id="wireguard_file" name="config_file" accept=".conf">
            <button type="submit" class="green-button">Загрузить и применить</button>
        </form>
        <!-- Кнопка перезагрузки службы WireGuard -->
        <button id="btnRestartWireguard" class="red-button">Перезагрузить службу WireGuard</button>
    </div>

    <?php
    // Проверяем наличие конфигурационного файла WireGuard
    $wireguardConfigFile = '/etc/wireguard/tun0.conf';
    if (file_exists($wireguardConfigFile)) {
        $content = file_get_contents($wireguardConfigFile);
        if (preg_match('/Endpoint\s*=\s*([^\s]+)/', $content, $matches)) {
            $endpoint = $matches[1];
            echo "<p style='text-align:center;'>Активный конфиг: tun0.conf (Endpoint: $endpoint)</p>";
        } else {
            echo "<p style='text-align:center;'>Активный конфиг: tun0.conf (Endpoint не найден)</p>";
        }
    } else {
        echo "<p style='text-align:center; color:red;'>Конфиг WireGuard не найден</p>";
    }
    ?>

    <!-- Консоль для вывода статуса службы WireGuard -->
    <div class="console">
        <pre><?php echo shell_exec('systemctl status wg-quick@tun0 --no-pager 2>&1'); ?></pre>
    </div>
</div>

<script>
document.getElementById('btnRestartWireguard').addEventListener('click', function() {
    if (confirm("Внимание! Перезагрузка службы WireGuard временно прервет соединение. Вы уверены, что хотите продолжить?")) {
        fetch('/scripts/restart_wireguard.php', { method: 'POST' })
            .then(response => response.text())
            .then(result => {
                alert(result);
                location.reload();
            })
            .catch(error => {
                alert("Ошибка при перезагрузке: " + error);
            });
    }
});
</script>
</body>
</html>
