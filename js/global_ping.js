// /var/www/html/js/global_ping.js

console.log('global_ping.js loaded'); // Отладочное сообщение

/**
 * Функция обновления глобального статуса пинга
 */
function updateGlobalPing() {
    fetch('/scripts/ajax_ping.php')
      .then(response => response.json())
      .then(data => {
          const el = document.getElementById('ping-status');
          if (el) {
              let color = 'green';
              if (data.ping > 80 && data.ping <= 100) {
                  color = 'yellow';
              } else if (data.ping > 100) {
                  color = 'red';
              }
              el.innerHTML = `ping: <span style="color:${color}; font-weight:bold;">${data.ping} ms</span>`;
          }
      })
      .catch(err => console.error('Error updating global ping:', err));
}

document.addEventListener('DOMContentLoaded', function() {
    updateGlobalPing();
    setInterval(updateGlobalPing, 5000); // обновление каждые 5 секунд
});
