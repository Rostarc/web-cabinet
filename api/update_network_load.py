#!/usr/bin/env python3
import psutil
import json
import time

# Путь к файлу, куда будут записываться метрики сети
OUTPUT_FILE = "/var/www/html/data/network_load.json"

# Первый замер
prev_counters = psutil.net_io_counters(pernic=True)
prev_time = time.time()

while True:
    # Задержка в 2 секунды между замерами
    time.sleep(2)
    current_counters = psutil.net_io_counters(pernic=True)
    current_time = time.time()
    dt = current_time - prev_time

    data = {"timestamp": int(current_time), "network_speed": {}}

    # Для каждого интерфейса вычисляем скорость передачи (байт/сек)
    for iface, current in current_counters.items():
        prev = prev_counters.get(iface)
        if prev is None:
            continue
        speed_sent = (current.bytes_sent - prev.bytes_sent) / dt
        speed_recv = (current.bytes_recv - prev.bytes_recv) / dt
        data["network_speed"][iface] = {
            "speed_sent": speed_sent,
            "speed_recv": speed_recv
        }
    # Записываем данные в JSON-файл
    with open(OUTPUT_FILE, "w") as f:
        json.dump(data, f)

    # Обновляем предыдущие значения
    prev_time = current_time
    prev_counters = current_counters
