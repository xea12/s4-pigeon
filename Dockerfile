FROM php:8.2-apache

# Instalacja zależności
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    default-mysql-client \
    ssl-cert \
    libcurl4-openssl-dev \
    uuid-dev \
    libssl-dev \
    libicu-dev \
    libidn2-0-dev \
    libidn11-dev \
    libevent-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*



# Instalacja rozszerzeń PHP
RUN docker-php-ext-install pdo pdo_mysql zip gd curl intl

# Install PECL extensions
RUN pecl install raphf && \
    docker-php-ext-enable raphf

RUN pecl install pecl_http && \
    docker-php-ext-enable http

RUN echo "extension=http.so" > /usr/local/etc/php/conf.d/docker-php-ext-http.ini


# Włączenie mod_rewrite i ssl
RUN a2enmod rewrite ssl



# Ustawienie katalogu roboczego
WORKDIR /var/www

# Kopiowanie plików projektu
COPY . /var/www

# Instalacja Composera
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalacja zależności Composera
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-scripts --no-autoloader

# Konfiguracja Apache
RUN sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/default-ssl.conf

# Ustawienie AllowOverride All
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Włączenie modułu SSL i witryny SSL
RUN a2enmod ssl
RUN a2ensite default-ssl

# Konfiguracja Apache dla certyfikatów SSL
RUN sed -i 's!SSLCertificateFile.*!SSLCertificateFile /etc/ssl/certs/fullchain.pem!' /etc/apache2/sites-available/default-ssl.conf
RUN sed -i 's!SSLCertificateKeyFile.*!SSLCertificateKeyFile /etc/ssl/certs/privkey.pem!' /etc/apache2/sites-available/default-ssl.conf


# Tworzenie .htaccess jeśli nie istnieje
RUN echo '<IfModule mod_rewrite.c>\n\
    RewriteEngine On\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteRule ^(.*)$ index.php [QSA,L]\n\
</IfModule>' > /var/www/public/.htaccess

# Zwiększ limity PHP
RUN echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 12288M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Konfiguracja Apache
RUN echo "LimitRequestBody 104857600" >> /etc/apache2/apache2.conf


RUN mkdir -p /var/www/var/log /var/www/var/cache && \
    chown -R www-data:www-data /var/www/var && \
    chmod -R 775 /var/www/var

RUN chown -R www-data:www-data /var/www/var/log /var/www/var/cache

RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

RUN chown -R www-data:www-data /var/www/templates
RUN chmod -R 755 /var/www/templates

# Utwórz katalog uploads i ustaw odpowiednie uprawnienia
RUN mkdir -p /var/www/public/uploads && \
    chown -R www-data:www-data /var/www/public/uploads && \
    chmod 775 /var/www/public/uploads

# Dodaj te linie na końcu Dockerfile'a, przed CMD
RUN mkdir -p /var/www/var/cache && \
    chown -R www-data:www-data /var/www/var/cache && \
    chmod -R 775 /var/www/var/cache

EXPOSE 80 443

CMD ["apache2-foreground"]