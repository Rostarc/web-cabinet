<?php
// /var/www/html/inc/openvpn.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление OpenVPN - VPN Panel</title>
    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        /* Центрирование заголовка */
        .header-title-custom {
            text-align: center;
            margin: 20px 0;
            font-size: 1.8em;
        }
        /* Стили для статуса службы */
        .status {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        /* Стили для консоли */
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
        /* Стили для кнопки перезагрузки службы */
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
    <h2 class="header-title-custom">Управление OpenVPN</h2>

    <?php
    // Получаем статус службы OpenVPN
    $openvpnStatus = trim(shell_exec('systemctl is-active openvpn@client1 2>/dev/null'));
    $statusText = ($openvpnStatus === 'active') ? "Активен" : "Выключен";
    $statusColor = ($openvpnStatus === 'active') ? "lime" : "red";
    ?>
    <div class="status">
        Статус OpenVPN: <span style="color: <?php echo $statusColor; ?>;"><?php echo $statusText; ?></span>
    </div>

    <div class="container" style="margin: 20px auto; text-align: center;">
        <form method="post" enctype="multipart/form-data" action="/scripts/upload_openvpn.php">
            <label for="openvpn_file">Выберите файл (.ovpn):</label>
            <input type="file" id="openvpn_file" name="config_file" accept=".ovpn">
            <button type="submit" class="green-button">Загрузить и применить</button>
        </form>
        <!-- Кнопка перезагрузки службы OpenVPN -->
        <button id="btnRestartOpenVPN" class="red-button">Перезагрузить службу OpenVPN</button>
    </div>

    <?php
    // Проверяем наличие конфигурационного файла
    $openvpnConfigFile = '/etc/openvpn/client1.conf';
    if (file_exists($openvpnConfigFile)) {
        $fileContent = file_get_contents($openvpnConfigFile);
        if (preg_match('/remote\s+(\d+\.\d+\.\d+\.\d+)/', $fileContent, $matches)) {
            $ip_address = $matches[1];
            echo "<p style='text-align:center;'>Активный конфиг: client1.conf (IP: $ip_address)</p>";
        } else {
            echo "<p style='text-align:center;'>Активный конфиг: client1.conf (IP не найден)</p>";
        }
    } else {
        echo "<p style='text-align:center; color:red;'>Конфиг OpenVPN не найден</p>";
    }
    ?>

    <!-- Консоль для вывода статуса службы OpenVPN -->
    <div class="console">
        <pre><?php echo shell_exec('systemctl status openvpn@client1 --no-pager 2>&1'); ?></pre>
    </div>
</div>

<script>
document.getElementById('btnRestartOpenVPN').addEventListener('click', function() {
    if (confirm("Внимание! Перезагрузка службы OpenVPN временно прервет соединение. Вы уверены, что хотите продолжить?")) {
        fetch('/scripts/restart_openvpn.php', { method: 'POST' })
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
