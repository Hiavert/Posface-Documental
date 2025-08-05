#!/bin/bash

set -x  # Muestra cada comando que ejecuta

sleep 10

php -v
which php

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage bootstrap/cache

php artisan storage:link || echo "Storage link ya existe"

php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

php -S 0.0.0.0:$PORT -t public

