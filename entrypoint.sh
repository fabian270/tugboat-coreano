#!/bin/sh
set -e

# Preparar el directorio de datos (SQLite) con permisos de Apache
mkdir -p /var/www/html/data
chown -R www-data:www-data /var/www/html/data

# Lanzar Apache en primer plano
exec apache2-foreground