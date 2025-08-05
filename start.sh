#!/bin/bash
set -x

# Esperar por dependencias
sleep 10

# Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Recompilar cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos y estructura de directorios
mkdir -p storage/app/public/tesis
chmod -R 775 storage bootstrap/cache
chmod -R 775 storage/app/public

# Manejo de enlace simbólico (robusto)
rm -rf public/storage
php artisan storage:link

# Si falla el comando oficial, crear manualmente
if [ ! -L "public/storage" ]; then
    echo "Creando enlace simbólico manualmente..."
    ln -s $PWD/storage/app/public public/storage
fi

# Verificar estructura de archivos
echo "=== CONTENIDO DE STORAGE ==="
ls -l storage/app/public/tesis
echo "=== ENLACE SIMBÓLICO ==="
ls -l public/storage

# Migraciones
php artisan migrate --force

# Iniciar servidor con configuración mejorada
php -S 0.0.0.0:$PORT -t public
