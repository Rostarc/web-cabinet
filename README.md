# VPN-Web-Installer

Веб-интерфейс для установки/смены файлов конфигурации OpenVPN/WireGuard

# Установка
Чтобы установить локальный сайт на сервер введите сначала:
```bash
sudo apt-get install -y htop net-tools mtr isc-dhcp-server network-manager wireguard openvpn apache2 php git iptables-persistent openssh-server resolvconf speedtest-cli nload libapache2-mod-php
```
А затем эту команду чтобы скачать файлы сайта:
```bash
sudo git clone https://github.com/Rostarc/VPN-Web-Installer.git /var/www/html
```
Чтобы войти на него перейдите по своему локальному адресу  http://192.168.X.X/ из компьютера в локальной сети

# Контакты и сотрудничество
Всегда готов обсудить условия для работы с вами и вашими решениями.

Есть VPN-конфигурации для ваших linux серверов, а также Windows/MacOs и Android/Ios.

Обращайтесь за помощью/вопросами в телеграмм - https://t.me/vpn_vendor
