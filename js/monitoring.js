// /var/www/html/js/monitoring.js

let pingConsole;
let pingChart2;
let pingIntervalId;

window.addEventListener('DOMContentLoaded', function() {
    pingConsole = document.getElementById('ping-console');
    initPingChart2();

    const btn = document.getElementById('btnPing');
    btn.addEventListener('click', () => {
        const target = document.getElementById('ping_target').value.trim();
        if (target) {
            startPinging(target);
        }
    });
});

function initPingChart2() {
  const ctx = document.getElementById('chartPing2').getContext('2d');
  pingChart2 = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [],
      datasets: [{
        label: 'Ping (ms)',
        data: [],
        borderColor: 'lime',
        backgroundColor: 'rgba(0,255,0,0.1)',
        tension: 0.2
      }]
    },
    options: {
      responsive: true,
      scales: {
        x: {
          ticks: {
            autoSkip: true,
            maxTicksLimit: 10
          }
        },
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

function startPinging(host) {
    // Если уже идет опрос — остановим
    if (pingIntervalId) clearInterval(pingIntervalId);

    // Очищаем консоль, обнуляем график
    pingConsole.innerHTML = '';
    pingChart2.data.labels = [];
    pingChart2.data.datasets[0].data = [];
    pingChart2.update();

    pingIntervalId = setInterval(() => {
        fetch('/scripts/ajax_ping_custom.php?host=' + encodeURIComponent(host))
            .then(resp => resp.json())
            .then(data => {
                appendPingLine(data.output);
                updateChart(data.ping);
            })
            .catch(e => {
                appendPingLine("[Error] " + e);
            });
    }, 2000); // <-- 2 секунды
}

function appendPingLine(line) {
    const div = document.createElement('div');
    div.textContent = line;
    pingConsole.appendChild(div);
    pingConsole.scrollTop = pingConsole.scrollHeight;
}

function updateChart(pingValue) {
    const now = new Date().toLocaleTimeString();
    pingChart2.data.labels.push(now);
    pingChart2.data.datasets[0].data.push(pingValue);

    // Ограничим историю, скажем, 120 точек (2 мин)
    if (pingChart2.data.labels.length > 120) {
        pingChart2.data.labels.shift();
        pingChart2.data.datasets[0].data.shift();
    }
    pingChart2.update();
}
