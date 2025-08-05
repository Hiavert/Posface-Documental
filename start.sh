#!/bin/bash

# Esperar a que la base de datos esté lista
sleep 10

# Cachear config y vistas en runtime
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones (aunque las tienes preparadas, dejamos el comando por compatibilidad)
php artisan migrate --force || true

# Iniciar FrankenPHP
SERVER_ADDR="0.0.0.0:$PORT" frankenphp php-server public/
