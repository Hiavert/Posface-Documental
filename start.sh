#!/bin/bash

sleep 10

echo "Puerto detectado: $PORT"

# Crear enlace de storage (ignorar si ya existe)
php artisan storage:link || echo "El enlace de storage ya existe"

# Optimizar la aplicación
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones (ignorar errores si ya están aplicadas)
php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

# Iniciar FrankenPHP correctamente
exec frankenphp php-server --address=0.0.0.0:$PORT public/

