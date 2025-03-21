<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>VPN Panel</title>
    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="main-container">
        <style>
            /* Дополнительные стили, если нужны */
            .os-info {
                background-color: #333;
                display: inline-block;
                padding: 6px 10px;
                border-radius: 6px;
                margin-bottom: 10px;
            }
        </style>

<?php
// /var/www/html/inc/home.php

// Определяем ОС и uptime
$osInfo = trim(shell_exec('lsb_release -d 2>/dev/null'));
if (!$osInfo) {
    $osRelease = @file_get_contents('/etc/os-release');
    if ($osRelease && preg_match('/^PRETTY_NAME="([^"]+)"/m', $osRelease, $m)) {
        $osInfo = "OS: " . $m[1];
    } else {
        $osInfo = "OS: неизвестная Linux";
    }
} else {
    $osInfo = str_replace('Description:', 'OS:', $osInfo);
}
$uptimeStr = trim(shell_exec('uptime -p 2>/dev/null'));
if (!$uptimeStr) {
    $uptimeStr = "Не удалось получить uptime.";
}

// VPN-сервисы
$ovpnStatus = trim(shell_exec('systemctl is-active openvpn@client1 2>/dev/null'));
$wgStatus   = trim(shell_exec('systemctl is-active wg-quick@tun0 2>/dev/null'));
function formatStatus($status) {
    return ($status === 'active')
        ? "<span style='color:lime;'>Активен</span>"
        : "<span style='color:red;'>Выключен</span>";
}
?>

<!-- Основное содержимое страницы -->
<div class="os-info">
    <strong><?php echo htmlspecialchars($osInfo); ?></strong><br>
    <em><?php echo htmlspecialchars($uptimeStr); ?></em>
</div>

<div style="margin-bottom:20px; text-align:center;">
    <h2>VPN-сервисы:</h2>
    <div style="display:flex; justify-content:center; gap:40px;">
        <div>
            <a href="/openvpn">OpenVPN</a>:
            <?php echo formatStatus($ovpnStatus); ?>
        </div>
        <div>
            <a href="/wireguard">WireGuard</a>:
            <?php echo formatStatus($wgStatus); ?>
        </div>
    </div>
</div>

<!-- Новый блок системных показателей -->
<div id="system-stats-new" class="system-stats-new">
    <h2>Системные показатели</h2>
    <div class="stat-item">
        <span>CPU:</span>
        <div class="progress-bar">
            <div id="cpu-progress" class="progress-fill" style="width:0%;">0%</div>
        </div>
    </div>
    <div class="stat-item">
        <span>RAM:</span>
        <div class="progress-bar">
            <div id="ram-progress" class="progress-fill" style="width:0%;">0%</div>
        </div>
    </div>
    <div class="stat-item">
        <span>Disk:</span>
        <div class="progress-bar">
            <div id="disk-progress" class="progress-fill" style="width:0%;">0 / 0</div>
        </div>
    </div>
</div>

<!-- Заголовок для Highcharts -->
<h2 style="text-align:center; margin-top:40px;">Мониторинг системы</h2>

<!-- Блок фильтра по времени и сброса зума -->
<div style="text-align:center; margin-bottom:10px;">
  <label for="selectRange">Период:</label>
  <select id="selectRange">
    <option value="300">5 минут</option>
    <option value="1800">30 минут</option>
    <option value="3600" selected>1 час</option>
    <option value="21600">6 часов</option>
    <option value="86400">24 часа</option>
  </select>
  <button id="btnApplyRange">Применить</button>

<!-- Сам график -->
<div id="chartCombined" style="max-width:900px; height:400px; margin: 0 auto;"></div>

<!-- Подключаем библиотеки Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<!-- (Дополнительно) <script src="https://code.highcharts.com/highcharts-more.js"></script> -->

<!-- Скрипт с логикой Highcharts (home_highcharts.js) -->
<script src="/js/home_highcharts.js"></script>

<!-- Скрипт со старыми прогресс-барами и пингом (home.js) -->
<script src="/js/home.js"></script>

<!-- (Опционально) Подключение chart.js, если нужно -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Раскрытие/скрытие таблицы Сетевые интерфейсы
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-ifaces');
    const tableDiv = document.getElementById('interfaces-table');
    toggleBtn.addEventListener('click', function() {
        if (tableDiv.style.display === 'none' || tableDiv.style.display === '') {
            tableDiv.style.display = 'block';
            toggleBtn.textContent = '[-]';
        } else {
            tableDiv.style.display = 'none';
            toggleBtn.textContent = '[+]';
        }
    });
});
</script>

    </div>

    <footer>
        <span>© 2025 VPN Panel v2.4.0</span>
        &nbsp;|&nbsp;
        <a href="https://t.me/vpn_vendor" target="_blank">
            Поддержка в Telegram
        </a>
    </footer>
</body>
</html>
