<?php
echo "<h2>Веб-консоль сервера</h2>";
?>

<div style="margin-bottom:10px;">
  <label>Цвет фона:</label>
  <input type="color" id="bgColorPicker" value="#000000" style="margin-right:20px;">

  <label>Цвет текста:</label>
  <input type="color" id="fgColorPicker" value="#ffffff">
</div>

<iframe id="shellFrame" src="/shell/" width="100%" height="600"></iframe>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const bgPicker = document.getElementById('bgColorPicker');
    const fgPicker = document.getElementById('fgColorPicker');
    const shellFrame = document.getElementById('shellFrame');

    // 1. Считываем сохранённые цвета из localStorage:
    let savedBg = localStorage.getItem('shellBg') || '#000000';
    let savedFg = localStorage.getItem('shellFg') || '#ffffff';

    bgPicker.value = savedBg;
    fgPicker.value = savedFg;

    // 2. Применяем цвета к iframe (если оно уже загружено)
    //   Немного задержим, чтобы контент успел загрузиться
    setTimeout(() => {
        applyShellColors(savedBg, savedFg);
    }, 1500);

    // 3. Обработчики изменения
    bgPicker.addEventListener('input', () => {
        let bg = bgPicker.value;
        let fg = fgPicker.value;
        localStorage.setItem('shellBg', bg);
        applyShellColors(bg, fg);
    });
    fgPicker.addEventListener('input', () => {
        let bg = bgPicker.value;
        let fg = fgPicker.value;
        localStorage.setItem('shellFg', fg);
        applyShellColors(bg, fg);
    });

    function applyShellColors(bg, fg) {
        if (!shellFrame.contentWindow) return;
        try {
            // Попробуем стилизовать body:
            let bodyDoc = shellFrame.contentDocument.body;
            bodyDoc.style.backgroundColor = bg;
            bodyDoc.style.color = fg;
            // Если есть элемент с id="vt100", стилизуем и его:
            const vt100 = shellFrame.contentDocument.getElementById('vt100');
            if(vt100) {
                vt100.style.backgroundColor = bg;
                vt100.style.color = fg;
            }
        } catch(e) {
            console.warn("Не удалось применить цвета:", e);
        }
    }
});
</script>

<!-- Подсказки для консоли -->
<div id="consoleHelp" style="margin-top:15px; padding:10px; background:#333; border:1px solid #555; color:#fff; border-radius:4px;">
  <h4>Горячие клавиши</h4>
  <ul style="list-style:none; padding:0; margin:0;">
    <li><span class="key">Ctrl</span> + <span class="key">Shift</span> + <span class="key">V</span> — Вставить скопированный текст</li>
    <li><span class="key">Ctrl</span> + <span class="key">Shift</span> + <span class="key">C</span> — Копировать текст</li>
  </ul>
  <h4 style="margin-top:10px;">Подсказки по командам</h4>
  <ul style="list-style:disc; padding-left:20px; margin:0;">
    <li><span class="key">Ctrl</span> + <span class="key">K</span> — удалить целую строку</li>
    <li><span class="key">Ctrl</span> + <span class="key">W</span> — поиск по тексту в файле</li>
    <li><span class="key">Ctrl</span> + <span class="key">X</span> — выйти из файла</li>
    <li>Полезные команды для администрирования: <span class="key">nload</span> <span class="key">speedtest</span>, <span class="key">ifconfig -a</span>, <span class="key">arp -a</span></li>
    <br>
  </ul>
</div>

