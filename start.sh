#!/bin/bash
set -x  # Muestra cada comando que ejecuta

# Esperar un poco por dependencias de Railway
sleep 10  

# Verificar PHP
php -v
which php

# Limpiar caches previos
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Recompilar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajustar permisos (incluyendo volumen)
chmod -R 775 storage bootstrap/cache

# Forzar recreación del symlink aunque exista
rm -f public/storage
php artisan storage:link || echo "No se pudo crear el symlink, pero continuamos"

# Migraciones de BD
php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

# Iniciar servidor
php -S 0.0.0.0:$PORT -t public

