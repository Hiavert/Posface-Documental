#!/bin/bash

sleep 10

php artisan migrate --force || echo "Migraciones ya aplicadas o error ignorado"

SERVER_ADDR="0.0.0.0:$PORT" frankenphp php-server public/
