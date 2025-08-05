#!/bin/bash

# Esperar a que la base de datos esté lista
sleep 10

# Crear enlace de storage
php artisan storage:link

# Optimizar la aplicación para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
php artisan migrate --force

# Iniciar FrankenPHP en el puerto correcto
echo "Iniciando FrankenPHP en el puerto: $PORT"
frankenphp run --listen "0.0.0.0:$PORT" public/


