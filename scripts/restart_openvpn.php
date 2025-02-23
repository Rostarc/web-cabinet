<?php
// /var/www/html/scripts/restart_openvpn.php
// Этот скрипт перезагружает службу OpenVPN.

header('Content-Type: text/plain');

// Выполняем перезагрузку службы OpenVPN
$output = shell_exec('sudo systemctl restart openvpn@client1 2>&1');
echo "OpenVPN перезагружен. Результат:\n" . $output;
