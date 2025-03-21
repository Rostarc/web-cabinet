// /var/www/html/js/home.js

document.addEventListener('DOMContentLoaded', function() {
    // Запускаем обновление пинга
    startLivePing();

    // Запускаем обновление системных показателей (прогресс-бары)
    updateSystemStatsNew();
    setInterval(updateSystemStatsNew, 10000);
});

// ===== Живое обновление пинга (каждые 2 секунды) =====
function startLivePing() {
    setInterval(() => {
        fetch('/scripts/ajax_ping.php')
            .then(response => response.json())
            .then(data => {
                updateTopPingStatus(data);
            })
            .catch(err => console.error('Live ping error:', err));
    }, 2000);
}

// Обновляет текст в #ping-status
function updateTopPingStatus(data) {
    const el = document.getElementById('ping-status');
    if (!el) return;

    const pingValue = data.ping || 0;
    let color = 'green';
    if (pingValue > 80 && pingValue <= 100) color = 'yellow';
    if (pingValue > 100) color = 'red';

    el.innerHTML = `ping: <span style="color:${color}; font-weight:bold;">${pingValue} ms</span>`;
}

// ===== Обновление системных показателей (прогресс-бары) =====
// Теперь берем данные из home_metrics_daemon.json и используем последнюю запись
function updateSystemStatsNew() {
    fetch('/data/home_metrics_daemon.json')
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) return;
            // Берем последнюю запись из массива
            let lastMeasurement = data[data.length - 1];

            // Значения CPU, RAM и Disk берутся напрямую
            let cpuVal  = Math.round(lastMeasurement.cpu);
            let ramVal  = Math.round(lastMeasurement.ram);
            let diskVal = Math.round(lastMeasurement.disk);

            // Обновляем прогресс-бар CPU
            const cpuProgress = document.getElementById('cpu-progress');
            if (cpuProgress) {
                cpuProgress.style.width = cpuVal + '%';
                cpuProgress.textContent = cpuVal + '%';
            }

            // Обновляем прогресс-бар RAM
            const ramProgress = document.getElementById('ram-progress');
            if (ramProgress) {
                ramProgress.style.width = ramVal + '%';
                ramProgress.textContent = ramVal + '%';
            }

            // Обновляем прогресс-бар Disk
            const diskProgress = document.getElementById('disk-progress');
            if (diskProgress) {
                diskProgress.style.width = diskVal + '%';
                diskProgress.textContent = diskVal + '%';
            }
        })
        .catch(err => console.error('Ошибка получения системной информации:', err));
}
