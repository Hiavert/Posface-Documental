#!/bin/bash

sleep 10
echo "Puerto detectado: $PORT"

# Crear enlace de storage
php artisan storage:link || echo "El enlace de storage ya existe"

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

# 🚀 Iniciar servidor PHP nativo (sin FrankenPHP)
exec php -S 0.0.0.0:$PORT -t public


