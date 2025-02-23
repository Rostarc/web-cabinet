<?php
// /var/www/html/inc/monitoring.php
?>
<!-- Заголовок страницы мониторинга -->
<h2 style="text-align:center;">График Ping</h2>

<!-- Комментарий (Beta-версия) -->
<p style="color:red; text-align:left; margin-left:10px; font-style:italic;">
    *Beta-версия страницы*
</p>
<p style="color:white; text-align:left; margin-left:10px;">
    Страница используется для более быстрой проверки пинга с частотой 2 секунды.
</p>
<p style="color:white; text-align:left; margin-left:10px;">
    Отличие от вкладки "Главная" в том, что вы выбираете, куда пинговать, и учет ведется только на этой странице без сохранения логов.
</p>

<!-- Контейнер для вывода пинга в режиме реального времени -->
<div id="ping-console" class="console-style"></div>

<!-- Поле для ввода домена/IP и кнопка Старт -->
<div style="text-align:center; margin:10px 0;">
    <label for="ping_target">Укажите домен/IP:</label>
    <input type="text" id="ping_target" value="google.com">
    <button id="btnPing" style="margin-left:8px;">Старт</button>
</div>

<!-- Канвас для графика пинга на странице мониторинга -->
<canvas id="chartPing2" style="display:block; margin:0 auto; max-width:900px; height:400px;"></canvas>

<!-- Подключение Chart.js и файла со скриптом мониторинга -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/js/monitoring.js"></script>
