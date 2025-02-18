<div class="content">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["config_file"])) {
    $allowed_extensions = array('conf');
    $file_extension = strtolower(pathinfo($_FILES["config_file"]["name"], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Разрешены только файлы с расширением .conf";
        exit();
    }

    shell_exec('sudo systemctl stop openvpn@tun0');
    shell_exec('sudo systemctl disable openvpn@tun0');
    shell_exec('sudo systemctl stop wg-quick@tun0');
    shell_exec('sudo systemctl disable wg-quick@tun0');
    shell_exec('sudo rm /etc/wireguard/*.conf');
    shell_exec('sudo rm /etc/openvpn/*.conf');

    $upload_dir = '/etc/wireguard/';
    $config_file_conf = $upload_dir . 'tun0.conf';

    if (move_uploaded_file($_FILES["config_file"]["tmp_name"], $config_file_conf)) {
        sleep(1);
        shell_exec('sudo systemctl enable wg-quick@tun0');
        shell_exec('sudo systemctl start wg-quick@tun0');
        sleep(4);
        echo "<script>Notice('WireGuard конфигурация успешно установлена и готова к работе!');</script>";
    } else {
        echo "Ошибка при загрузке файла.";
    }
}
include_once 'get_ip.php';
?>
<br>
<div class="container">
    <h2>Установка и запуск WireGuard</h2>
    <form method="post" enctype="multipart/form-data" class="container-form">
        <label for="config_file">Выберите файл конфигурации (только *.conf):</label><br>
        <input type="file" id="config_file" name="config_file" accept=".conf">
        <input type="hidden" name="menu" value="wireguard">
        <input type="submit" class="green-button" value="Установить и запустить">
    </form>
</div>
</div>
