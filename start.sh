#!/bin/bash

# Esperar a que la base de datos esté lista
until nc -z -v -w30 $DB_HOST $DB_PORT
do
  echo "Esperando a la base de datos..."
  sleep 5
done

# Ejecutar migraciones
php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

# Iniciar FrankenPHP
SERVER_ADDR="0.0.0.0:$PORT" frankenphp php-server public/

