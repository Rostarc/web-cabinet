# VPN-Web-Installer

Веб-интерфейс для установки/смены файлов конфигурации OpenVPN/WireGuard

# Установка
Чтобы установить локальный сайт на сервер введите сначала:
```bash
sudo wget https://raw.githubusercontent.com/Rostarc/VPN-Setup-Script/main/VPN-Setup-Ubuntu20.04-22.04.sh -O VPN-Setup-Ubuntu20.04-22.04.sh && sudo bash VPN-Setup-Ubuntu20.04-22.04.sh
```
Удаляем старый файлы:
```bash
sudo rm -r /var/www/html
```
Переходим в дерикторию и клонируем репозиторий
```bash
cd /var/www/
```
```bash
sudo git clone https://github.com/Rostarc/web-cabinet.git /var/www/html
```
Чтобы войти на него перейдите по своему локальному адресу  http://192.168.X.X/ из компьютера в локальной сети

# Контакты и сотрудничество
Всегда готов обсудить условия для работы с вами и вашими решениями.

Есть VPN-конфигурации для ваших linux серверов, а также Windows/MacOs и Android/Ios.

Обращайтесь за помощью/вопросами в телеграмм - https://t.me/vpn_vendor
