<?php
// /var/www/html/scripts/restart_wireguard.php
// Этот скрипт перезагружает службу WireGuard.

header('Content-Type: text/plain');

$output = shell_exec('sudo systemctl restart wg-quick@tun0 2>&1');
echo "WireGuard перезагружен. Результат:\n" . $output;
