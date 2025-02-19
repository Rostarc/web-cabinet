<?php
// /var/www/html/scripts/upload_openvpn.php

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["config_file"])) {
    $upload_dir = '/etc/openvpn/';
    $file = $_FILES["config_file"];
    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($ext !== "ovpn") {
        echo "Ошибка: Загрузите файл с расширением .ovpn";
        exit;
    }

    $config_file = $upload_dir . 'client1.conf';

    if (move_uploaded_file($file["tmp_name"], $config_file)) {
        // Отключаем WireGuard
        shell_exec('sudo systemctl stop wg-quick@tun0');
        shell_exec('sudo systemctl disable wg-quick@tun0');
        shell_exec('sudo rm -f /etc/wireguard/tun0.conf');

        // Перезапускаем OpenVPN
        shell_exec('sudo systemctl enable openvpn@client1.service');
        shell_exec('sudo systemctl restart openvpn@client1.service');

        // Возврат на страницу OpenVPN
        header("Location: /index.php?page=openvpn");
        exit;
    } else {
        echo "Ошибка при загрузке файла.";
    }
} else {
    echo "Ошибка: Неверный запрос.";
}
