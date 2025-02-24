#!/bin/bash
# /var/www/html/scripts/update.sh

# Перейти в каталог сайта
cd /var/www/html || exit

# Убедимся, что каталог отмечен как безопасный для Git (если требуется)
sudo git config --global --add safe.directory /var/www/html

# Получить последние изменения из репозитория
sudo git fetch origin main

# Сбросить все локальные изменения и обновить рабочую копию до состояния ветки main
sudo git reset --hard origin/main

echo "Скрипт обновления завершён"
