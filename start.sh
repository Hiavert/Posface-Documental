#!/bin/bash
set -e

echo "Puerto detectado: $PORT"

# Esperar un poco a que la DB esté disponible
sleep 10

# Enlaces y optimizaciones de Laravel
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Ejecutar migraciones sin detener el arranque si ya existen
php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

# Iniciar FrankenPHP en el puerto que Railway espera
exec SERVER_ADDR="0.0.0.0:$PORT" frankenphp php-server public/