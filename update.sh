#!/bin/bash

# Путь к директории с веб-сайтом
WEB_DIR="/var/www/html/vpn-web-installer"
BACKUP_DIR="/var/www/html/vpn-web-installer/backup"
UPDATE_DIR="$WEB_DIR/updates"

# Сделаем резервную копию текущей версии
echo "Создаю резервную копию..."
mkdir -p $BACKUP_DIR
cp -r $WEB_DIR/* $BACKUP_DIR

# Проверка наличия интернет-соединения
if ping -q -c 1 -W 1 google.com >/dev/null; then
    echo "Интернет-соединение доступно, проверка обновлений..."

    # Получаем последнюю версию из репозитория
    git -C $WEB_DIR pull origin main

    # Применение обновлений
    if [ $? -eq 0 ]; then
        echo "Обновление успешно завершено."
    else
        echo "Ошибка обновления, откатываю изменения..."
        cp -r $BACKUP_DIR/* $WEB_DIR/ # Откат к старой версии
    fi
else
    echo "Нет интернет-соединения, обновления не могут быть получены."
fi
