// js/local_network.js (обновлённая версия)
(function() {
  // Глобальная переменная для хранения полного списка устройств
  let fullDevices = [];
  // Объект для хранения текущего состояния сортировки
  let currentSort = { field: null, ascending: true };

  // Функция для определения иконки устройства по строке производителя
  function getDeviceIcon(vendor) {
    const vendorLower = vendor.toLowerCase();
    if (vendorLower.includes("router") || vendorLower.includes("tplink") || vendorLower.includes("netgear") ||
        vendorLower.includes("d-link") || vendorLower.includes("asus") || vendorLower.includes("ubiquiti")) {
      return '<img src="/img/devices/router.png" alt="Router" style="width:24px; height:24px;">';
    } else if (vendorLower.includes("windows")) {
      return '<img src="/img/devices/windows.png" alt="Windows" style="width:24px; height:24px;">';
    } else if (vendorLower.includes("linux") || vendorLower.includes("ubuntu") || vendorLower.includes("debian") ||
               vendorLower.includes("fedora") || vendorLower.includes("centos")) {
      return '<img src="/img/devices/linux.png" alt="Linux" style="width:24px; height:24px;">';
    } else if (vendorLower === "(unknown)" || vendorLower.trim() === "") {
      return '<img src="/img/devices/unknown.png" alt="Unknown" style="width:24px; height:24px;">';
    } else {
      return '<img src="/img/devices/computer.png" alt="Computer" style="width:24px; height:24px;">';
    }
  }

  // Функция отрисовки основной таблицы (отображаются только первые 3 устройства)
  function renderMainTable(devices) {
    const tbody = document.getElementById('localNetworkTable').querySelector('tbody');
    tbody.innerHTML = "";
    const visibleDevices = devices.slice(0, 3);
    visibleDevices.forEach(device => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${getDeviceIcon(device.vendor)}</td>
        <td>${device.ip}</td>
        <td>${device.mac}</td>
        <td>${device.vendor}</td>
      `;
      tbody.appendChild(tr);
    });
    document.getElementById('localNetworkCount').textContent = devices.length;
    // Показываем кнопку "Показать все", если устройств больше 3
    document.getElementById('showAllContainer').style.display = devices.length > 3 ? 'block' : 'none';
  }

  // Функция отрисовки полного списка в модальном окне
  function renderFullTable(devices) {
    const modalTbody = document.getElementById('fullLocalNetworkTable').querySelector('tbody');
    modalTbody.innerHTML = "";
    devices.forEach(device => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${getDeviceIcon(device.vendor)}</td>
        <td>${device.ip}</td>
        <td>${device.mac}</td>
        <td>${device.vendor}</td>
      `;
      modalTbody.appendChild(tr);
    });
  }

  // Функция сортировки массива устройств по заданному полю
  function sortDevices(devices, field, ascending) {
    return devices.sort((a, b) => {
      let valA = a[field].toUpperCase();
      let valB = b[field].toUpperCase();
      if (valA < valB) {
        return ascending ? -1 : 1;
      }
      if (valA > valB) {
        return ascending ? 1 : -1;
      }
      return 0;
    });
  }

  // Функция применения фильтра и сортировки, затем обновление отображения
  function updateDevicesDisplay() {
    const filterInput = document.getElementById('localFilterInput');
    const filterValue = filterInput ? filterInput.value.trim().toLowerCase() : "";
    let filteredDevices = fullDevices.filter(device => {
      return device.ip.toLowerCase().includes(filterValue) ||
             device.mac.toLowerCase().includes(filterValue) ||
             device.vendor.toLowerCase().includes(filterValue);
    });
    if (currentSort.field) {
      filteredDevices = sortDevices(filteredDevices, currentSort.field, currentSort.ascending);
    }
    renderMainTable(filteredDevices);
    // Если модальное окно открыто – обновляем и его содержимое
    if (document.getElementById('fullLocalDevicesModal').style.display === 'block') {
      renderFullTable(filteredDevices);
    }
  }

  // Функция загрузки списка устройств из /data/local_network.json
  function fetchLocalNetworkDevices() {
    fetch('/data/local_network.json')
      .then(response => response.json())
      .then(data => {
        if (data.devices && data.devices.length > 0) {
          fullDevices = data.devices;
          updateDevicesDisplay();
        } else {
          fullDevices = [];
          updateDevicesDisplay();
        }
      })
      .catch(error => console.error("Ошибка при получении данных локальной сети:", error));
  }

  // Настройка обработчика кнопки "Показать все"
  function setupShowAllButton() {
    const showAllButton = document.getElementById('showAllButton');
    if (showAllButton) {
      showAllButton.addEventListener('click', function() {
        renderFullTable(fullDevices);
        document.getElementById('fullLocalDevicesModal').style.display = 'block';
      });
    }
  }

  // Настройка обработчика закрытия модального окна
  function setupCloseModal() {
    const closeButton = document.querySelector('#fullLocalDevicesModal button');
    if (closeButton) {
      closeButton.addEventListener('click', function() {
        document.getElementById('fullLocalDevicesModal').style.display = 'none';
      });
    }
  }

  // Настройка фильтрации – обработчик поля фильтра
  function setupFilterInput() {
    const filterInput = document.getElementById('localFilterInput');
    if (filterInput) {
      filterInput.addEventListener('input', updateDevicesDisplay);
    }
  }

  // Настройка сортировки по заголовкам таблицы в модальном окне
  function setupSorting() {
    const headers = document.querySelectorAll('#fullLocalNetworkTable thead th');
    headers.forEach((header, index) => {
      header.style.cursor = 'pointer';
      header.addEventListener('click', function() {
        // По столбцам: индекс 1 – IP, 2 – MAC, 3 – Производитель.
        let field;
        if (index === 1) {
          field = 'ip';
        } else if (index === 2) {
          field = 'mac';
        } else if (index === 3) {
          field = 'vendor';
        } else {
          return; // для колонки с иконкой сортировка не применяется
        }
        if (currentSort.field === field) {
          currentSort.ascending = !currentSort.ascending;
        } else {
          currentSort.field = field;
          currentSort.ascending = true;
        }
        updateDevicesDisplay();
      });
    });
  }

  // Обработчик кнопки для принудительного сканирования локальной сети
  function setupForceScanButton() {
    const forceButton = document.getElementById('forceScanButton');
    if (forceButton) {
      forceButton.addEventListener('click', function() {
        forceButton.disabled = true;
        forceButton.textContent = "Сканирование...";
        fetch('/api/scan_local_network.py')
          .then(response => {
            // Независимо от ответа, считаем, что сканирование запущено
            forceButton.disabled = false;
            forceButton.textContent = "Запустить сканирование локальной сети";
            fetchLocalNetworkDevices();
            alert("Сканирование выполнено.");
          })
          .catch(error => {
            console.error("Ошибка при выполнении сканирования:", error);
            forceButton.disabled = false;
            forceButton.textContent = "Запустить сканирование локальной сети";
            alert("Ошибка при выполнении сканирования.");
          });
      });
    }
  }

  // Первоначальная настройка – вызывается при загрузке документа
  document.addEventListener('DOMContentLoaded', function() {
    fetchLocalNetworkDevices();
    setupShowAllButton();
    setupCloseModal();
    setupFilterInput();
    setupSorting();
    setupForceScanButton();
    // Обновлять список каждые 6 часов (6 * 60 * 60 * 1000 мс)
    setInterval(fetchLocalNetworkDevices, 6 * 60 * 60 * 1000);
  });

  // Для отладки можно вызвать window.refreshLocalNetworkDevices()
  window.refreshLocalNetworkDevices = fetchLocalNetworkDevices;
})();
