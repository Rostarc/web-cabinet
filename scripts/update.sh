#!/bin/bash
# /var/www/html/scripts/update.sh

cd /var/www/html
sudo git config --global --add safe.directory /var/www/html
sudo git pull origin main

echo "Скрипт обновления завершён"
