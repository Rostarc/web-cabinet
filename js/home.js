// /var/www/html/js/home.js

// ===== Глобальные переменные =====
let combinedChart = null;  // Комбинированный график для Ping, CPU, RAM и Disk
let topPingTimer = null;   // Таймер для обновления верхнего статуса пинга

// ===== Запуск после загрузки страницы =====
document.addEventListener('DOMContentLoaded', function() {
    initCombinedChart();
    startLivePing();
    startSystemStats();
    initPingHistoryFilter();
});

function updateSystemStatsNew() {
    fetch('/scripts/ajax_sysinfo.php')
        .then(response => response.json())
        .then(data => {
            // Ожидается, что data содержит: cpu, ram, diskUsed, diskTotal
            let cpuVal = parseFloat(data.cpu);
            let ramVal = parseFloat(data.ram);

            // Для диска: если diskTotal содержит буквы (например, "100G"), parseFloat вернёт число до буквы.
            let diskUsed = parseFloat(data.diskUsed);
            let diskTotal = parseFloat(data.diskTotal);
            let diskPerc = diskTotal ? Math.round((diskUsed / diskTotal) * 100) : 0;

            // Обновляем CPU
            const cpuProgress = document.getElementById('cpu-progress');
            if (cpuProgress) {
                cpuProgress.style.width = cpuVal + '%';
                cpuProgress.textContent = cpuVal + '%';
            }

            // Обновляем RAM
            const ramProgress = document.getElementById('ram-progress');
            if (ramProgress) {
                ramProgress.style.width = ramVal + '%';
                ramProgress.textContent = ramVal + '%';
            }

            // Обновляем Disk
            const diskProgress = document.getElementById('disk-progress');
            if (diskProgress) {
                diskProgress.style.width = diskPerc + '%';
                diskProgress.textContent = diskUsed + ' / ' + data.diskTotal;
            }
        })
        .catch(err => console.error('Ошибка получения системной информации:', err));
}


// Запускаем обновление каждые 10 секунд и вызываем сразу при загрузке
setInterval(updateSystemStatsNew, 10000);
updateSystemStatsNew();

// ===== Инициализация комбинированного графика =====
function initCombinedChart() {
    // Элемент <canvas id="chartCombined"> должен присутствовать в HTML
    const ctx = document.getElementById('chartCombined').getContext('2d');
    combinedChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {   // Ping
                    label: 'Ping (ms)',
                    data: [],
                    borderColor: 'green',  // начальный цвет – зелёный
                    backgroundColor: 'rgba(0,255,0,0.1)',
                    tension: 0.2,
                    spanGaps: true
                },
                {   // CPU
                    label: 'CPU (%)',
                    data: [],
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0,0,255,0.1)',
                    tension: 0.2,
                    spanGaps: true
                },
                {   // RAM
                    label: 'RAM (%)',
                    data: [],
                    borderColor: 'purple',
                    backgroundColor: 'rgba(128,0,128,0.1)',
                    tension: 0.2,
                    spanGaps: true
                },
                {   // Disk Usage (%)
                    label: 'Disk Usage (%)',
                    data: [],
                    borderColor: 'teal',
                    backgroundColor: 'rgba(0,128,128,0.1)',
                    tension: 0.2,
                    spanGaps: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
}

// ===== Живое обновление пинга (каждые 5 секунд) =====
function startLivePing() {
    setInterval(() => {
        fetch('/scripts/ajax_ping.php')
            .then(response => response.json())
            .then(data => {
                updateTopPingStatus(data);
                updateCombinedChartPing(data.ping);
            })
            .catch(err => console.error('Live ping error:', err));
    }, 5000);
}

function updateCombinedChartPing(ping) {
    const now = new Date().toLocaleTimeString();
    // Добавляем новую метку и значение пинга
    combinedChart.data.labels.push(now);
    combinedChart.data.datasets[0].data.push(ping);
    // Для остальных показателей (CPU, RAM, Disk) добавляем пустые значения, чтобы сохранить синхронность
    combinedChart.data.datasets[1].data.push(null);
    combinedChart.data.datasets[2].data.push(null);
    combinedChart.data.datasets[3].data.push(null);

    // Динамически изменяем цвет линии пинга в зависимости от значения:
    let color = 'green';
    let bgColor = 'rgba(0,255,0,0.1)';
    if (ping > 80 && ping <= 120) {
        color = 'yellow';
        bgColor = 'rgba(255,255,0,0.1)';
    } else if (ping > 120) {
        color = 'red';
        bgColor = 'rgba(255,0,0,0.1)';
    }
    combinedChart.data.datasets[0].borderColor = color;
    combinedChart.data.datasets[0].backgroundColor = bgColor;

    // Ограничиваем историю до 20 точек
    if (combinedChart.data.labels.length > 20) {
        combinedChart.data.labels.shift();
        combinedChart.data.datasets.forEach(ds => ds.data.shift());
    }
    combinedChart.update();
}

// ===== Обновление системных показателей (CPU, RAM, Disk) каждые 10 секунд =====
// Функция для обновления комбинированного графика системных показателей (история)
function updateCombinedChartSysStats() {
    fetch('/scripts/ajax_sysstats.php')
        .then(response => response.json())
        .then(data => {
            // Обновляем график с системными данными, например, для последних 20 точек
            // Здесь можно реализовать фильтрацию по времени, если потребуется
            // Пример: заменяем данные для CPU, RAM и Disk
            const nowLabels = data.map(row => {
                const d = new Date(row.time * 1000);
                return `${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`;
            });
            // Обновляем только системные показатели (datasets 1, 2, 3)
            // Например, если combinedChart.data.labels уже заполнены пингом, можно обновлять отдельно
            // Либо можно сделать отдельный график для системных данных
            // Здесь приведён пример обновления данных системных показателей:
            combinedChart.data.datasets[1].data = data.map(row => row.cpu);
            combinedChart.data.datasets[2].data = data.map(row => row.ram);
            combinedChart.data.datasets[3].data = data.map(row => row.diskPerc);
            // Можно также обновить метки, если требуется:
            combinedChart.data.labels = nowLabels;
            combinedChart.update();
        })
        .catch(err => console.error('SysStats filter error:', err));
}

// Можно вызывать эту функцию по кнопке или с определённой периодичностью, например:
setInterval(updateCombinedChartSysStats, 10000);


function startSystemStats() {
    setInterval(() => {
        fetch('/scripts/ajax_sysinfo.php')
            .then(response => response.json())
            .then(data => updateCombinedChartSystem(data))
            .catch(err => console.error('System stats error:', err));
    }, 10000);
}

function updateCombinedChartSystem(data) {
    const now = new Date().toLocaleTimeString();
    // Добавляем новую метку
    combinedChart.data.labels.push(now);
    // Для пинга добавляем пустое значение, так как обновляем только системные показатели
    combinedChart.data.datasets[0].data.push(null);
    // CPU и RAM данные
    combinedChart.data.datasets[1].data.push(data.cpu);
    combinedChart.data.datasets[2].data.push(data.ram);
    // Вычисляем процент использования диска (если diskTotal не определён, берем 1 для избежания деления на 0)
    let used = parseFloat(data.diskUsed);
    let total = parseFloat(data.diskTotal) || 1;
    let diskPercent = Math.round((used / total) * 100);
    combinedChart.data.datasets[3].data.push(diskPercent);

    if (combinedChart.data.labels.length > 20) {
        combinedChart.data.labels.shift();
        combinedChart.data.datasets.forEach(ds => ds.data.shift());
    }
    combinedChart.update();
}

// ===== Обновление статуса пинга (верхний угол) =====
function updateTopPingStatus(data) {
    const el = document.getElementById('ping-status');
    if (!el) return;
    let color = 'green';
    if (data.ping > 80 && data.ping <= 100) color = 'yellow';
    if (data.ping > 100) color = 'red';
    el.innerHTML = `ping: <span style="color:${color}; font-weight:bold;">${data.ping} ms</span>`;
}

// ===== Фильтр по времени для истории пинга =====
function initPingHistoryFilter() {
    const btnRange = document.getElementById('btnRangeApply');
    const selRange = document.getElementById('selectRange');

    btnRange.addEventListener('click', function() {
        const rangeSec = selRange.value;
        fetch(`/scripts/ajax_ping_history_range.php?range=${rangeSec}`)
            .then(response => response.json())
            .then(data => {
                // При фильтрации заменяем данные пинга (dataset 0) и метки
                combinedChart.data.labels = [];
                combinedChart.data.datasets[0].data = [];
                data.forEach(row => {
                    const ts = row.time;
                    const ping = row.ping;
                    const dateObj = new Date(ts * 1000);
                    const hh = String(dateObj.getHours()).padStart(2, '0');
                    const mm = String(dateObj.getMinutes()).padStart(2, '0');
                    const ss = String(dateObj.getSeconds()).padStart(2, '0');
                    const label = `${hh}:${mm}:${ss}`;
                    combinedChart.data.labels.push(label);
                    combinedChart.data.datasets[0].data.push(ping);
                });
                // Обновляем цвет линии пинга в зависимости от последнего значения
                if (data.length > 0) {
                    const lastPing = data[data.length - 1].ping;
                    let color = 'green';
                    let bgColor = 'rgba(0,255,0,0.1)';
                    if (lastPing > 80 && lastPing <= 120) {
                        color = 'yellow';
                        bgColor = 'rgba(255,255,0,0.1)';
                    } else if (lastPing > 120) {
                        color = 'red';
                        bgColor = 'rgba(255,0,0,0.1)';
                    }
                    combinedChart.data.datasets[0].borderColor = color;
                    combinedChart.data.datasets[0].backgroundColor = bgColor;
                }
                combinedChart.update();
            })
            .catch(err => console.error('History filter error:', err));
    });
}
