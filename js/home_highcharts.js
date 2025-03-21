// /var/www/html/js/home_highcharts.js

let myChart = null;
// Храним общие данные, чтобы не перезагружать при фильтрации
let fullSysData  = []; // [{time, cpu, ram, disk}, ...]
let fullPingData = []; // [{time, ping}, ...]

document.addEventListener('DOMContentLoaded', () => {
  initHighcharts();
  initUIHandlers();
  loadAllData();

  // Обновляем каждые 2 секунды
  setInterval(loadAllData, 2000);
});

// ------------------------
// 1) Инициализация графика
// ------------------------
function initHighcharts() {
  Highcharts.setOptions({
    chart: {
      backgroundColor: '#2f2f2f',
      style: { fontFamily: 'sans-serif' }
    },

    // ------ ДОБАВЛЯЕМ lang ------
    lang: {
      contextButtonTitle: "Меню графика",
      printChart: "Распечатать",
      downloadPNG: "Скачать PNG",
      downloadJPEG: "Скачать JPEG",
      downloadPDF: "Скачать PDF",
      downloadSVG: "Скачать SVG",
      viewFullscreen: "На весь экран"
    },
    title:     { style: { color: '#fff' } },
    subtitle:  { style: { color: '#fff' } },
    xAxis:     { labels: { style: { color: '#fff' } } },
    yAxis: {
      labels: { style: { color: '#fff' } },
      title: {
        style: { color: '#fff' },
        text: 'Показатели'
      }
    },
    legend: {
      itemStyle:      { color: '#fff' },
      itemHoverStyle: { color: '#ddd' }
    },
    tooltip: {
      backgroundColor: '#ffffff',
      style: { color: '#000000' }
    }
  });

    // ------ Создаем график ------
  myChart = Highcharts.chart('chartCombined', {
    chart: {
      type: 'spline',
      zoomType: 'x',  // <-- drag-zoom по оси X
      resetZoomButton: {
        theme: {
          fill: '#f0f0f0',
          style: {
            color: 'black'
          }
        },
        position: {
          align: 'right',
          x: -10,
          y: 10
        }
      }
    },
    title:    { text: 'История системных метрик' },
    subtitle: { text: 'CPU, RAM, Disk, Ping' },
    xAxis:    { type: 'datetime' },
    yAxis:    {
      min: 0,
      max: 100
    },
    series: [
      { name: 'CPU (%)',   data: [] },
      { name: 'RAM (%)',   data: [] },
      { name: 'Disk (%)',  data: [] },
      { name: 'Ping (ms)', data: [] }
    ]
  });
}

// ----------------------------------------
// 2) Инициализируем элементы (кнопка «Применить»)
// ----------------------------------------
function initUIHandlers() {
  const btnApplyRange = document.getElementById('btnApplyRange');
  if (btnApplyRange) {
    btnApplyRange.addEventListener('click', () => {
      applyTimeFilter();
    });
  }
}

// ---------------------------------------------------------------------------------
// 3) Загружаем все данные (системные + пинг) — вызывается каждые 2 сек
// ---------------------------------------------------------------------------------
function loadAllData() {
  Promise.all([
    fetch('/data/home_metrics_daemon.json').then(r => r.json()),
    fetch('/scripts/ajax_ping_history.php').then(r => r.json())
  ])
  .then(([sysData, pingData]) => {
    // Сохраняем в глобальные массивы
    fullSysData  = sysData;
    fullPingData = pingData;
    // Фильтруем по текущему selectRange (по умолчанию 1 час)
    applyTimeFilter();
  })
  .catch(err => console.error('Ошибка загрузки:', err));
}

// --------------------------------------------------
// 4) Фильтруем по времени, обновляем серии
// --------------------------------------------------
function applyTimeFilter() {
  const sel = document.getElementById('selectRange');
  if (!sel) {
    // Нет фильтра -> показываем все данные
    updateSeries(fullSysData, fullPingData);
    return;
  }
  const rangeSec = parseInt(sel.value, 10) || 86400; // по умолчанию 1 час
  const now = Math.floor(Date.now() / 1000);

  // Порог по времени
  const cutoff = now - rangeSec;

  // Фильтруем
  const filteredSys  = fullSysData.filter(row => row.time >= cutoff);
  const filteredPing = fullPingData.filter(row => row.time >= cutoff);

  updateSeries(filteredSys, filteredPing);
}

// -------------------------
// 5) Обновляем 4 ряда графика
// -------------------------
function updateSeries(sysData, pingData) {
  const cpuSeries  = [];
  const ramSeries  = [];
  const diskSeries = [];
  const pingSeries = [];

  sysData.forEach(row => {
    const ms = row.time * 1000;
    cpuSeries.push([ ms, row.cpu ]);
    ramSeries.push([ ms, row.ram ]);
    diskSeries.push([ ms, row.disk ]);
  });

  pingData.forEach(row => {
    const ms = row.time * 1000;
    pingSeries.push([ ms, row.ping ]);
  });

  // Обновляем
  myChart.series[0].setData(cpuSeries,  false);
  myChart.series[1].setData(ramSeries,  false);
  myChart.series[2].setData(diskSeries, false);
  myChart.series[3].setData(pingSeries, false);

  myChart.redraw();
}
