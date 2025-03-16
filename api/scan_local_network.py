#!/usr/bin/env python3
import subprocess
import json
import re

def scan_network(interface='enp0s8'):
    try:
        # Запускаем arp-scan для указанного интерфейса
        result = subprocess.run(['sudo', 'arp-scan', '--interface=' + interface, '--localnet'],
                                capture_output=True, text=True, timeout=30)
        output = result.stdout
    except Exception as e:
        return {"error": str(e)}

    devices = []
    # Пример строки: "192.168.1.10    00:11:22:33:44:55    Some Vendor Inc."
    pattern = re.compile(r'(\d+\.\d+\.\d+\.\d+)\s+([0-9a-f:]+)\s+(.*)')
    for line in output.splitlines():
        m = pattern.match(line)
        if m:
            ip = m.group(1)
            mac = m.group(2)
            vendor = m.group(3).strip()
            devices.append({"ip": ip, "mac": mac, "vendor": vendor})
    return {"devices": devices}

if __name__ == '__main__':
    data = scan_network('enp0s8')  # замените на нужный интерфейс
    output_file = "/var/www/html/data/local_network.json"
    with open(output_file, "w") as f:
        json.dump(data, f)
