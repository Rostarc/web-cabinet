<?php
$openvpn_config_file = '/etc/openvpn/client1.conf';

if (file_exists($openvpn_config_file)) {
    $file_content = file_get_contents($openvpn_config_file);

    // Ищем строку с "remote" и берем IP-адрес из этой строки
    if (preg_match('/remote\s+([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $file_content, $matches)) {
        $ip_address = $matches[1];
    } else {
        $ip_address = 'IP не найден';
    }

    echo "Файл: client1.conf<br>";
    echo "IP: $ip_address<br>";
    echo "<span class='active-status'>OpenVPN конфиг активен</span>";
} else {
    echo "<span class='inactive-status'>Конфиг OpenVPN не найден</span>";
}
?>
