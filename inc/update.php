<?php
// /var/www/html/inc/update.php
echo "<h2>Обновление сайта</h2>";

if (isset($_POST['do_update'])) {
    // Вызываем скрипт
    $output = shell_exec('sudo /var/www/html/scripts/update.sh 2>&1');
    echo "<pre>$output</pre>";
    echo "<p>Обновление выполнено.</p>";
} else {
    echo '
    <form method="post">
        <button type="submit" name="do_update" class="green-button">
            Обновить из Git
        </button>
    </form>
    ';
}
