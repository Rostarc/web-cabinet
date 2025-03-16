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
    /* Общие стили для логов */
    .log-section {
      margin-bottom: 30px;
    }
    .log-title {
      font-size: 1.4em;
      margin-bottom: 5px;
      text-align: center;
    }
    .log-info {
      text-align: center;
      font-size: 0.9em;
      color: #555;
      margin-bottom: 10px;
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
    .refresh-button, .toggle-tab-button {
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
    .refresh-button:hover, .toggle-tab-button:hover {
      background-color: #45a049;
    }
    /* Стили для вкладок */
    .tab-container {
      margin-bottom: 10px;
      text-align: center;
    }
    .tab-container button {
      margin: 0 5px;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
  </style>
</head>
<body>
<div class="main-container">
  <h2 style="text-align:center;">Логи</h2>

<div style="text-align:center; margin-bottom:20px;">
  <a href="/export_logs.php" class="refresh-button" style="background-color:#4caf50; font-size:16px; padding:10px 20px; text-decoration:none;">
    Выгрузить логи
  </a>
</div>

  <!-- Секция: Последние входы в систему -->
  <div class="log-section" id="log-logins">
    <div class="log-title">Последние входы в систему</div>
    <div class="tab-container">
      <button class="toggle-tab-button" onclick="showTab('logins', 'readable')">Понятный вид</button>
      <button class="toggle-tab-button" onclick="showTab('logins', 'extended')">Расширенный вид</button>
    </div>
    <div id="logins-readable" class="tab-content">
      <pre class="log-content">
<?php
// Получаем сырой лог входов (команда last -n 20)
$raw_logins = shell_exec('last -n 20');
// Преобразуем в понятный вид и сортируем по времени (новейшие первыми)
$lines = explode("\n", trim($raw_logins));
$entries = array();
foreach ($lines as $line) {
    if (empty($line)) continue;
    // Пример строки: "qwerty   pts/0        Sat Feb 22 14:02 - crash  (05:21)     192.168.1.101"
    if (preg_match('/^(\S+)\s+\S+\s+(\w+\s+\w+\s+\d+\s+\d+:\d+)\s+\-\s+(\S+)\s+\(([^)]+)\)\s*(\S+)?/', $line, $m)) {
        $user = $m[1];
        $timeStr = $m[2];
        $statusField = $m[3];
        $duration = $m[4];
        $ip = isset($m[5]) ? $m[5] : "через терминал сервера";
        $status = (stripos($statusField, "crash") !== false) ? "не удачно" : "удачно";
        $timestamp = strtotime($timeStr);
        $entries[] = array(
            'timestamp' => $timestamp,
            'output' => "[Вход $timeStr] Логин - $user, статус: $status, IP - $ip"
        );
    } else {
        $entries[] = array('timestamp' => 0, 'output' => $line);
    }
}
usort($entries, function($a, $b) {
    return $b['timestamp'] - $a['timestamp'];
});
foreach ($entries as $entry) {
    echo $entry['output'] . "\n";
}
?>
      </pre>
    </div>
    <div id="logins-extended" class="tab-content">
      <pre class="log-content">
<?php
echo $raw_logins;
?>
      </pre>
    </div>
  </div>

  <!-- Секция: DHCP Лог -->
  <div class="log-section" id="log-dhcp">
    <div class="log-title">DHCP Лог</div>
    <div class="tab-container">
      <button class="toggle-tab-button" onclick="showTab('dhcp', 'readable')">Понятный вид</button>
      <button class="toggle-tab-button" onclick="showTab('dhcp', 'extended')">Расширенный вид</button>
    </div>
    <div id="dhcp-readable" class="tab-content">
      <pre class="log-content">
<?php
// Выводим статус DHCP без цветового оформления
$raw_dhcp = shell_exec("sudo systemctl status isc-dhcp-server --no-pager 2>&1");
if ($raw_dhcp) {
    if (preg_match('/Active:\s+(\S+)\s+\(([^)]+)\)\s+since\s+([^\n]+)/', $raw_dhcp, $m)) {
        $activeStatus = $m[1];
        $result = $m[2];
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
    </div>
    <div id="dhcp-extended" class="tab-content">
      <pre class="log-content">
<?php
echo $raw_dhcp;
?>
      </pre>
    </div>
  </div>

  <!-- Секция: OpenVPN Лог -->
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

  <!-- Секция: WireGuard Лог -->
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

  <!-- Секция: Системные показатели (с фильтрацией) -->
  <div class="log-section" id="log-sysstats">
    <div class="log-title">Системные показатели (последние 50 строк)</div>
    <div class="log-info">
      Логов может не быть. Тут записи CPU / RAM / Disk где хотя бы один параметр превышает 50%.
    </div>
    <div class="tab-container">
      <button class="toggle-tab-button" onclick="showTab('sysstats', 'readable')">Понятный вид</button>
      <button class="toggle-tab-button" onclick="showTab('sysstats', 'extended')">Расширенный вид</button>
    </div>
    <div id="sysstats-readable" class="tab-content">
      <pre class="log-content">
<?php
if (file_exists('/var/log/sys_stats.log')) {
    // Читаем последние 50 строк, переворачиваем порядок (новейшие первыми)
    $lines = file('/var/log/sys_stats.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_slice($lines, -50);
    $lines = array_reverse($lines);
    // Выводим только уникальные записи, где хотя бы один параметр (CPU, RAM или Disk) > 49%
    $unique = array();
    foreach ($lines as $line) {
        $parts = preg_split('/\s+/', $line);
        if (count($parts) >= 4) {
            $ts = (int)$parts[0];
            $cpu = floatval($parts[1]);
            $ram = floatval($parts[2]);
            $disk = floatval($parts[3]);
            if ($cpu > 49 || $ram > 49 || $disk > 49) {
                $date = date("d.m.Y H:i", $ts);
                $output = "[$date] - CPU: {$cpu}%, RAM: {$ram}%, Disk: {$disk}%";
                $unique[] = $output;
            }
        }
    }
    $unique = array_unique($unique);
    $unique = array_slice($unique, 0, 25);
    echo implode("\n", $unique);
} else {
    echo "Лог системных показателей не найден.";
}
?>
      </pre>
    </div>
    <div id="sysstats-extended" class="tab-content">
      <pre class="log-content">
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

  <!-- Секция: Системный журнал (syslog) -->
  <div class="log-section">
    <div class="log-title">Системный журнал (syslog, последние 50 строк)</div>
    <button class="refresh-button" onclick="location.reload();">Обновить лог</button>
    <pre class="log-content">
<?php
if (file_exists('/var/log/syslog')) {
    $syslog = shell_exec("sudo tail -n 50 /var/log/syslog");
    $lines = explode("\n", trim($syslog));
    $lines = array_reverse($lines);
    echo implode("\n", $lines);
} else {
    echo "Системный журнал не найден.";
}
?>
    </pre>
  </div>

  <!-- Секция: Лог веб-сервера -->
  <div class="log-section">
    <div class="log-title">Лог веб-сервера</div>
    <button class="refresh-button" onclick="location.reload();">Обновить лог</button>
    <pre class="log-content">
<?php
if (file_exists('/var/log/apache2/error.log')) {
    $apache_error = shell_exec("sudo tail -n 50 /var/log/apache2/error.log");
    $lines = explode("\n", trim($apache_error));
    $lines = array_reverse($lines);
    echo implode("\n", $lines);
} else {
    echo "Лог веб-сервера не найден.";
}
?>
    </pre>
  </div>

</div>

<script>
function showTab(section, tab) {
    var readable = document.getElementById(section + '-readable');
    var extended = document.getElementById(section + '-extended');
    if (readable && extended) {
        if(tab === 'readable') {
            readable.classList.add('active');
            extended.classList.remove('active');
        } else if(tab === 'extended') {
            extended.classList.add('active');
            readable.classList.remove('active');
        }
    }
}
// По умолчанию показываем понятный вид для секций с вкладками
document.addEventListener('DOMContentLoaded', function() {
    showTab('logins', 'readable');
    showTab('dhcp', 'readable');
    showTab('sysstats', 'readable');
});
</script>
</body>
</html>
