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
            // Допустим, у Shellinabox корневой элемент #vt100 или body
            // Попробуем стилизовать body:
            let bodyDoc = shellFrame.contentDocument.body;
            bodyDoc.style.backgroundColor = bg;
            bodyDoc.style.color = fg;
            // Можно стилизовать #vt100, #console, #alt_console, если нужно
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
