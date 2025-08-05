#!/bin/bash

# Esperar BD
sleep 10

# 🔹 Limpiar cachés antes de optimizar
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 🔹 Volver a optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 🔹 Storage link
php artisan storage:link || true

# 🔹 Migraciones
php artisan migrate --force || echo "Migraciones ya aplicadas"

# 🔹 Iniciar servidor
php -S 0.0.0.0:$PORT -t public

