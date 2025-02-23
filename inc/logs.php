<?php
// /var/www/html/inc/logs.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Логи - VPN Panel</title>
  <link rel="stylesheet" href="/css/styles.css">
  <style>
    .log-section {
      margin-bottom: 30px;
    }
    .log-title {
      font-size: 1.4em;
      margin-bottom: 5px;
      text-align: center;
    }
    .log-content {
      background-color: #000;
      color: #0f0;
      font-family: "Courier New", monospace;
      padding: 10px;
      border-radius: 4px;
      max-height: 300px;
      overflow-y: auto;
      white-space: pre-wrap;
    }
    .refresh-button, .toggle-view-button {
      display: inline-block;
      margin: 5px;
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      background-color: #4caf50;
      color: #fff;
      font-size: 14px;
    }
    .refresh-button:hover, .toggle-view-button:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
<div class="main-container">
  <h2 style="text-align:center;">Логи</h2>
  
  <!-- Раздел "Последние входы в систему" -->
  <div class="log-section" id="log-logins">
    <div class="log-title">Последние входы в систему</div>
    <div>
      <button class="toggle-view-button" onclick="toggleView('logins', 'readable')">Понятный вид</button>
      <button class="toggle-view-button" onclick="toggleView('logins', 'extended')">Расширенный вид</button>
    </div>
    <pre class="log-content" id="logins-readable" style="display: none;">
<?php
// Получаем сырой лог входов (команда last -n 20)
$raw_logins = shell_exec('last -n 20');
// Преобразуем в понятный вид
$lines = explode("\n", trim($raw_logins));
foreach ($lines as $line) {
    if (empty($line)) continue;
    // Пример строки: "qwerty   pts/0        Sat Feb 22 14:02 - crash  (05:21)     192.168.1.101"
    if (preg_match('/^(\S+)\s+\S+\s+(\w+\s+\w+\s+\d+\s+\d+:\d+)\s+\-\s+(\S+)\s+\(([^)]+)\)\s*(\S+)?/', $line, $m)) {
        $user = $m[1];
        $time = $m[2];
        $statusField = $m[3];
        $duration = $m[4];
        $ip = isset($m[5]) ? $m[5] : "через терминал сервера";
        $status = (stripos($statusField, "crash") !== false) ? "не удачно" : "удачно";
        echo "[Вход $time] Логин - $user, статус: $status, IP - $ip\n";
    } else {
        echo $line."\n";
    }
}
?>
    </pre>
    <pre class="log-content" id="logins-extended">
<?php
echo $raw_logins;
?>
    </pre>
  </div>
  
  <!-- Раздел "DHCP Лог" -->
  <div class="log-section">
    <div class="log-title">DHCP Лог</div>
    <div>
      <button class="toggle-view-button" onclick="toggleView('dhcp', 'readable')">Понятный вид</button>
      <button class="toggle-view-button" onclick="toggleView('dhcp', 'extended')">Расширенный вид</button>
    </div>
    <pre class="log-content" id="dhcp-readable" style="display: none;">
<?php
// Получаем сырой статус службы isc-dhcp-server
$raw_dhcp = shell_exec("sudo systemctl status isc-dhcp-server --no-pager 2>&1");
if ($raw_dhcp) {
    // Попытаемся извлечь строку "Active:"
    if (preg_match('/Active:\s+(\S+)\s+\(([^)]+)\)\s+since\s+([^\n]+)/', $raw_dhcp, $m)) {
        $activeStatus = $m[1]; // например, "failed"
        $result = $m[2]; // например, "exit-code"
        $since = $m[3];
        echo "[DHCP $since] - Статус: $activeStatus ($result)";
    } else {
        echo "Не удалось определить статус службы.";
    }
} else {
    echo "Статус службы DHCP недоступен.";
}
?>
    </pre>
    <pre class="log-content" id="dhcp-extended">
<?php
echo $raw_dhcp;
?>
    </pre>
  </div>
  
  <!-- Раздел "OpenVPN" (оставляем как есть) -->
  <div class="log-section">
    <div class="log-title">OpenVPN (последние 30 строк)</div>
    <button class="refresh-button" onclick="location.reload();">Обновить лог</button>
    <pre class="log-content">
<?php
$ovpn_log = shell_exec("sudo journalctl -u openvpn@client1 --no-pager -n 30 2>&1");
echo $ovpn_log;
?>
    </pre>
  </div>
  
  <!-- Раздел "WireGuard" (оставляем как есть) -->
  <div class="log-section">
    <div class="log-title">WireGuard (последние 30 строк)</div>
    <button class="refresh-button" onclick="location.reload();">Обновить лог</button>
    <pre class="log-content">
<?php
$wg_log = shell_exec("sudo journalctl -u wg-quick@tun0 --no-pager -n 30 2>&1");
echo $wg_log;
?>
    </pre>
  </div>
  
  <!-- Раздел "Системные показатели" -->
  <div class="log-section">
    <div class="log-title">Системные показатели (последние 50 строк)</div>
    <div>
      <button class="toggle-view-button" onclick="toggleView('sysstats', 'readable')">Понятный вид</button>
      <button class="toggle-view-button" onclick="toggleView('sysstats', 'extended')">Расширенный вид</button>
    </div>
    <pre class="log-content" id="sysstats-readable" style="display: none;">
<?php
if (file_exists('/var/log/sys_stats.log')) {
    $lines = file('/var/log/sys_stats.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = preg_split('/\s+/', $line);
        if (count($parts) >= 4) {
            $ts = (int)$parts[0];
            $cpu = $parts[1];
            $ram = $parts[2];
            $disk = $parts[3];
            $date = date("d.m.Y/H:i", $ts);
            echo "[CPU $date] - $cpu%\n";
            echo "[RAM $date] - $ram%\n";
            echo "[Disk $date] - $disk%\n";
            echo "---------------------------------\n";
        }
    }
} else {
    echo "Лог системных показателей не найден.";
}
?>
    </pre>
    <pre class="log-content" id="sysstats-extended">
<?php
if (file_exists('/var/log/sys_stats.log')) {
    echo shell_exec("tail -n 50 /var/log/sys_stats.log");
} else {
    echo "Лог системных показателей не найден.";
}
?>
    </pre>
  </div>
  
</div>
<script>
function toggleView(section, view) {
    if(section === 'logins') {
        document.getElementById('logins-readable').style.display = (view === 'readable') ? 'block' : 'none';
        document.getElementById('logins-extended').style.display = (view === 'extended') ? 'block' : 'none';
    } else if(section === 'dhcp') {
        document.getElementById('dhcp-readable').style.display = (view === 'readable') ? 'block' : 'none';
        document.getElementById('dhcp-extended').style.display = (view === 'extended') ? 'block' : 'none';
    } else if(section === 'sysstats') {
        document.getElementById('sysstats-readable').style.display = (view === 'readable') ? 'block' : 'none';
        document.getElementById('sysstats-extended').style.display = (view === 'extended') ? 'block' : 'none';
    }
}
</script>
</body>
</html>
