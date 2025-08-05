#!/bin/bash

# Esperar a que la base de datos esté lista (opcional pero recomendado)
sleep 10

# Ejecutar migraciones
php artisan migrate --force

# Iniciar FrankenPHP
SERVER_ADDR="0.0.0.0:$PORT" frankenphp php-server public/