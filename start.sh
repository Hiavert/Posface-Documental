#!/bin/bash

# Mostrar versión de PHP para depuración
which php
php -v

# Esperar a que la base de datos esté lista (puedes ajustar el tiempo)
sleep 10

# Crear enlace de storage
php artisan storage:link

# Optimizar la aplicación para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

# Iniciar FrankenPHP con puerto dinámico
SERVER_ADDR="0.0.0.0:$PORT" frankenphp php-server public/


