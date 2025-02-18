<?php
function validate_openvpn_config($file) {
    $content = file_get_contents($file);
    // Проверка синтаксиса OpenVPN конфигурации (пример)
    if (strpos($content, 'client') !== false && strpos($content, 'proto') !== false) {
        return true;
    } else {
        return false;
    }
}

if ($_FILES['config_file']['error'] == 0) {
    $uploaded_file = $_FILES['config_file']['tmp_name'];
    if (validate_openvpn_config($uploaded_file)) {
        echo "Конфигурация валидна!";
    } else {
        echo "Ошибка: неверный формат конфигурации!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["config_file"])) {
    $upload_dir = '/etc/openvpn/';
    $file = $_FILES["config_file"];
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($file_extension != "ovpn") {
        echo "Ошибка: Загрузите файл с расширением .ovpn";
        exit();
    }

    $config_file = $upload_dir . 'client1.conf';

    // Перемещаем загруженный файл в нужную директорию
    if (move_uploaded_file($file["tmp_name"], $config_file)) {
        // Отключение и удаление WireGuard
        shell_exec('sudo systemctl stop wg-quick@tun0');
        shell_exec('sudo systemctl disable wg-quick@tun0');
        shell_exec('sudo rm /etc/wireguard/tun0.conf');

        // Перезапуск службы OpenVPN
        shell_exec('sudo systemctl enable openvpn@client1.service');
        shell_exec('sudo systemctl restart openvpn@client1.service');

        echo "Файл конфигурации OpenVPN успешно загружен и применен.";
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
