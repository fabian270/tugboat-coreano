FROM php:8.3-apache

# Instalar extensión SQLite vía PDO + habilitar mod_rewrite
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Permitir .htaccess (rutas limpias) y servir public/ como raíz web
RUN printf '<Directory /var/www/html>\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
        > /etc/apache2/conf-available/allow-override.conf \
    && a2enconf allow-override \
    && printf '<VirtualHost *:80>\n    ServerName localhost\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        Options Indexes FollowSymLinks\n        AllowOverride All\n        Require all granted\n    </Directory>\n    ErrorLog ${APACHE_LOG_DIR}/error.log\n    CustomLog ${APACHE_LOG_DIR}/access.log combined\n</VirtualHost>\n' \
        > /etc/apache2/sites-available/000-default.conf

# Copiar la aplicación y el entrypoint
COPY app/ /var/www/html/
COPY entrypoint.sh /entrypoint.sh
RUN chown -R www-data:www-data /var/www/html \
    && chmod +x /entrypoint.sh

WORKDIR /var/www/html

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]