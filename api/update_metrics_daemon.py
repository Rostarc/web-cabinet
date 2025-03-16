#!/usr/bin/env python3
import psutil
import json
import time

output_file = "/var/www/html/data/system_metrics.json"

while True:
    data = {}
    data["timestamp"] = int(time.time())
    # CPU
    data["cpu"] = {
        "cpu_percent": psutil.cpu_percent(interval=0.5),
        "per_cpu_percent": psutil.cpu_percent(interval=0.5, percpu=True)
    }
    # Память
    vm = psutil.virtual_memory()
    data["memory"] = {
        "total": vm.total,
        "available": vm.available,
        "percent": vm.percent,
        "used": vm.used,
        "free": vm.free
    }
    # Диск (корневой раздел)
    du = psutil.disk_usage('/')
    data["disk"] = {
        "total": du.total,
        "used": du.used,
        "free": du.free,
        "percent": du.percent
    }
    # Сетевые I/O метрики
    net_io = psutil.net_io_counters(pernic=True)
    data["network_io"] = {}
    for iface, counters in net_io.items():
        data["network_io"][iface] = {
            "bytes_sent": counters.bytes_sent,
            "bytes_recv": counters.bytes_recv,
            "packets_sent": counters.packets_sent,
            "packets_recv": counters.packets_recv,
            "errin": counters.errin,
            "errout": counters.errout,
            "dropin": counters.dropin,
            "dropout": counters.dropout
        }
    # Статус сетевых интерфейсов
    net_stats = psutil.net_if_stats()
    data["network_status"] = {}
    for iface, stat in net_stats.items():
        data["network_status"][iface] = {
            "isup": stat.isup,
            "duplex": stat.duplex,
            "speed": stat.speed,
            "mtu": stat.mtu
        }
    # Температуры (если доступны)
    try:
        temps = psutil.sensors_temperatures()
        data["temperatures"] = {}
        for sensor, entries in temps.items():
            data["temperatures"][sensor] = [entry._asdict() for entry in entries]
    except Exception as e:
        data["temperatures"] = "Not available: " + str(e)

    # Записываем данные в файл (перезаписываем, оставляем только последние данные)
    with open(output_file, "w") as f:
        json.dump(data, f)
    time.sleep(2)
