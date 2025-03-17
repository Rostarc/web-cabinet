Веб-интерфейс для установки/смены файлов конфигурации OpenVPN/WireGuard

# Установка
Чтобы установить локальный сайт на сервер введите сначала (устанавливается через другой скрипт):
```bash
wget https://raw.githubusercontent.com/vpn-vendor/vpn/main/vpn.sh -O vpn.sh && sudo bash vpn.sh
```

Но если вдруг вы хотите проигнорировать создание нужных файлов, назначение прав и прочее тогда можно сделать вот так.
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

Визуальный вид панели:
![Без имени](https://github.com/user-attachments/assets/909157e0-124a-4958-b1ed-286a076161ce)

![image](https://github.com/user-attachments/assets/9a2be4c9-2463-4e7a-82d8-a66a0d65cb25)

![image](https://github.com/user-attachments/assets/9f28a94d-b16f-4577-b808-980199a315d7)


# Контакты и сотрудничество
Всегда готов обсудить условия для работы с вами и вашими решениями.

Есть VPN-конфигурации для ваших linux серверов, а также Windows/MacOs и Android/Ios.

Обращайтесь за помощью/вопросами в телеграмм - https://t.me/vpn_vendor
