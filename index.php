<!DOCTYPE html>
<html>
<head>
    <title>Переустановка VPN</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="main-container">
    <div id="server-ip" class="info-field">Текущий IP сервера: <?php echo trim(shell_exec('curl -s ifconfig.me')); ?></div>
    <h2>Переустановка VPN</h2>
    <div class="vpn-section">
        <!-- OpenVPN Section -->
        <div class="container">
            <h3>Загрузить OpenVPN</h3>
            <form method="post" enctype="multipart/form-data" action="upload_openvpn.php">
                <label for="openvpn_file">Выберите файл</label>
                <input type="file" id="openvpn_file" name="config_file">
                <div id="openvpn-info" class="info-field"><?php include 'openvpn_info.php'; ?></div>
                <button type="submit" class="green-button">Сменить конфиг</button>
            </form>
        </div>

        <!-- WireGuard Section -->
        <div class="container">
            <h3>Загрузить WireGuard</h3>
            <form method="post" enctype="multipart/form-data" action="upload_wireguard.php">
                <label for="wireguard_file">Выберите файл</label>
                <input type="file" id="wireguard_file" name="config_file">
                <div id="wireguard-info" class="info-field"><?php include 'wireguard_info.php'; ?></div>
                <button type="submit" class="green-button">Сменить конфиг</button>
            </form>
        </div>
    </div>
</div>

<!-- Липкая строка для статуса обновлений -->
<div id="update-status" style="position: fixed; bottom: 10px; right: 10px; background-color: #333; color: white; padding: 10px; border-radius: 5px;">
    <span id="site-version">Загрузка...</span>
</div>

<script>
    // Запрашиваем статус обновлений
    fetch('/check_for_update.php')
        .then(response => response.json())
        .then(data => {
            const updateStatus = document.getElementById('update-status');
            const versionText = document.getElementById('site-version');

            if (data.update_available) {
                versionText.textContent = 'Новая версия доступна! (Текущая: ' + data.local_version + ', Последняя: ' + data.remote_version + ')';
                updateStatus.style.backgroundColor = '#ff3333'; // Красный для обновлений
            } else {
                versionText.textContent = 'Актуальная версия';
                updateStatus.style.backgroundColor = '#33cc33'; // Зеленый для актуальной версии
            }
        })
        .catch(() => {
            const versionText = document.getElementById('site-version');
            versionText.textContent = 'Ошибка связи с сервером обновлений';
            updateStatus.style.backgroundColor = '#ff9900'; // Желтый для ошибки
        });
</script>

<style>
.vpn-section {
    display: flex;
    justify-content: space-between;
}
</style>

</body>
</html>
