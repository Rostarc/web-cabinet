<?php
// inc/update.php – обновление сайта через веб-интерфейс
echo "<h2 style='text-align:center;'>Обновление сайта</h2>";

if (isset($_POST['do_update'])) {
    echo "<div style='text-align:center; margin-bottom:1em;'>Выполняется обновление, пожалуйста подождите...</div>";
    // Запускаем скрипт обновления
    $output = shell_exec('sudo /opt/update_site.sh 2>&1');
    echo "<pre style='background:#111; color:#0f0; padding:1em; border-radius:5px; max-height:300px; overflow-y:auto; text-align:left;'>" . htmlspecialchars($output) . "</pre>";
    echo "<p style='text-align:center; color:lime;'>Обновление выполнено.</p>";
} else {
    echo '
    <form method="post" style="text-align:center;">
        <button type="submit" name="do_update" class="green-button">
            Обновить из Git
        </button>
    </form>
    ';
}
?>
