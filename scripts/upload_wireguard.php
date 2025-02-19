<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["config_file"])) {
    $upload_dir = '/etc/wireguard/';
    $file = $_FILES["config_file"];
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($file_extension != "conf") {
        echo "Ошибка: Загрузите файл с расширением .conf";
        exit();
    }

    $config_file = $upload_dir . 'tun0.conf';

    // Перемещаем загруженный файл в нужную директорию
    if (move_uploaded_file($file["tmp_name"], $config_file)) {
        // Отключение OpenVPN
        shell_exec('sudo systemctl stop openvpn@client1.service');
        shell_exec('sudo systemctl disable openvpn@client1.service');
        shell_exec('sudo rm /etc/openvpn/client1.conf');

        // Перезапуск службы WireGuard
        shell_exec('sudo systemctl enable wg-quick@tun0');
        shell_exec('sudo systemctl restart wg-quick@tun0');

        echo "Файл конфигурации WireGuard успешно загружен и применен.";
        // Перенаправление обратно на главную страницу
        header("Location: index.php");
        exit();
    } else {
        echo "Ошибка при загрузке файла.";
    }
} else {
    echo "Ошибка: Неверный запрос.";
}
?>
