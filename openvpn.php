<div class="content">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["config_file"])) {
    $allowed_extensions = array('ovpn');
    $file_extension = strtolower(pathinfo($_FILES["config_file"]["name"], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Разрешены только файлы с расширением .ovpn";
        exit();
    }

    shell_exec('sudo systemctl stop wg-quick@tun0');
    shell_exec('sudo systemctl disable wg-quick@tun0');
    shell_exec('sudo rm /etc/wireguard/*.conf');
    shell_exec('sudo rm /etc/openvpn/*.conf');

    $upload_dir = '/etc/openvpn/';
    $config_file_ovpn = $upload_dir . "tun0.conf";
    $config_file_conf = $upload_dir . pathinfo($config_file_ovpn, PATHINFO_FILENAME) . ".conf";

    if (move_uploaded_file($_FILES["config_file"]["tmp_name"], $config_file_ovpn)) {
        if (rename($config_file_ovpn, $config_file_conf)) {
            shell_exec('sudo systemctl stop openvpn@tun0');
            sleep(1);
            $service_name = pathinfo($config_file_conf, PATHINFO_FILENAME);
            shell_exec('sudo systemctl disable openvpn@tun0');
            shell_exec('sudo systemctl enable openvpn@tun0');
            shell_exec('sudo systemctl start openvpn@tun0');
            sleep(4);
            echo "<script>Notice('OpenVPN конфигурация успешно установлена и готова к работе!');</script>";
        } else {
            echo "Ошибка при переименовании файла.";
        }
    } else {
        echo "Ошибка при загрузке файла.";
    }
}
include_once 'get_ip.php';
?>
<br>

<div class="container">
    <h2>Установка и запуск OpenVPN</h2>
    <form method="post" enctype="multipart/form-data" class="container-form">
        <label for="config_file">Выберите файл конфигурации (только *.ovpn):</label><br>
        <input type="file" id="config_file" name="config_file" accept=".ovpn">
        <input type="hidden" name="menu" value="openvpn">
        <input type="submit" class="green-button" value="Установить и запустить">
    </form>
</div>
</div>
