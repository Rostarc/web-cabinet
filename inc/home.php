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
    return ($status === 'active') ? "<span style='color:lime;'>Активен</span>" : "<span style='color:red;'>Выключен</span>";
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
            <a href="/openvpn">OpenVPN</a>: <?php echo formatStatus($ovpnStatus); ?>
        </div>
        <div>
            <a href="/wireguard">WireGuard</a>: <?php echo formatStatus($wgStatus); ?>
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

</style>

<h2 style="text-align:center; margin-top:20px;">
    Сетевые интерфейсы
    <span id="toggle-ifaces" style="cursor:pointer; font-size:0.9em;">[+]</span>
</h2>
<div id="interfaces-table" style="display:none; margin-bottom:20px;">
    <table style="border-collapse: collapse; width:80%; margin: 0 auto;">
        <tr style="background: #444;">
            <th>№</th>
            <th>Интерфейс</th>
            <th>MAC-адрес</th>
            <th>Айпи IPv4</th>
            <th>Айпи IPv6</th>
        </tr>
        <tr style="border-bottom:1px solid #555;">
            <td>1</td>
            <td>enp0s3</td>
            <td>08:00:27:e4:6f:80</td>
            <td>10.0.2.15/24</td>
            <td>fe80::a00:27ff:fee4:6f80/64</td>
        </tr>
        <tr style="border-bottom:1px solid #555;">
            <td>2</td>
            <td>enp0s8</td>
            <td>08:00:27:d0:95:bd</td>
            <td>192.168.1.105/24</td>
            <td>fe80::a00:27ff:fed0:95bd/64</td>
        </tr>
    </table>
</div>

<!-- Комбинированный график для мониторинга системы -->
<h2 style="text-align:center; margin-top:40px;">Мониторинг системы</h2>
<canvas id="chartCombined" style="display:block; margin:0 auto; max-width:900px; height:400px;"></canvas>

<!-- Фильтр по времени для истории пинга -->
<div style="text-align:center; margin-bottom:10px;">
    <label for="selectRange">Период:</label>
    <select id="selectRange">
        <option value="300">Последние 5 минут</option>
        <option value="900">15 минут</option>
        <option value="1800">30 минут</option>
        <option value="3600" selected>1 час</option>
        <option value="10800">3 часа</option>
        <option value="86400">24 часа</option>
    </select>
    <button id="btnRangeApply">Показать</button>
</div>

<!-- Подключение внешних скриптов -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/js/home.js"></script>
<script>
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
        <span>© 2025 VPN Panel v1.0</span>
        &nbsp;|&nbsp;
        <a href="https://t.me/vpn_vendor" target="_blank">
            Поддержка в Telegram
        </a>
    </footer>
</body>
</html>
