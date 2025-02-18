<?php
$config_file = '/etc/wireguard/tun0.conf';
if (file_exists($config_file)) {
    $content = file_get_contents($config_file);
    if (preg_match('/Endpoint\s*=\s*([^\s]+)/', $content, $matches)) {
        $ip_address = $matches[1];
        echo "Файл: tun0.conf<br>IP: $ip_address<br><span class='active-status'>WireGuard конфиг активен</span>";
    } else {
        echo "Файл: tun0.conf<br><span class='active-status'>WireGuard конфиг активен, IP не найден</span>";
    }
} else {
    echo "<span class='inactive-status'>Конфиг WireGuard не найден</span>";
}
?>
