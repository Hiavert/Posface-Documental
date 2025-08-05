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

# Crear directorio si no existe (para Railway)
mkdir -p storage/app/public/tesis

# Crear enlace simbólico (con reintento)
php artisan storage:link || {
    echo "Fallo al crear storage link - reintentando con método manual"
    rm -f public/storage
    ln -s $PWD/storage/app/public public/storage
}

# Verificar que el enlace existe
ls -la public

# Migraciones de BD
php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

# Iniciar servidor
php -S 0.0.0.0:$PORT -t public
