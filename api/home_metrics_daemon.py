#!/usr/bin/env python3
import psutil
import json
import time
import os

output_file = "/var/www/html/data/home_metrics_daemon.json"

while True:
    # 1) Готовим новую запись
    timestamp = int(time.time())
    cpu_val = psutil.cpu_percent(interval=0.5)
    mem = psutil.virtual_memory()
    ram_val = mem.percent
    du = psutil.disk_usage('/')
    disk_val = du.percent

    new_entry = {
        "time": timestamp,
        "cpu": cpu_val,
        "ram": ram_val,
        "disk": disk_val
        # При желании сразу добавляйте ping, если он есть
        # "ping": ...
    }

    # 2) Считываем старый массив (если файл есть и в нём JSON-данные)
    if os.path.exists(output_file):
        try:
            with open(output_file, "r") as f:
                data_array = json.load(f)
                # ожидаем, что data_array — это список
        except:
            data_array = []
    else:
        data_array = []

    # 3) Добавляем новую запись в конец
    data_array.append(new_entry)

    # 4) При желании ограничиваем длину массива, чтобы он не рос бесконечно:
    max_len = 86400  # хранить последние 86400 записей
    if len(data_array) > max_len:
        data_array = data_array[-max_len:]

    # 5) Сохраняем обратно
    with open(output_file, "w") as f:
        json.dump(data_array, f)

    # Ждём пару секунд
    time.sleep(2)
