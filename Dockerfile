FROM php:8.4-apache

# install needed software
RUN DEBIAN_FRONTEND=noninteractive apt-get -y update && \
    apt-get -y install npm unzip libzip-dev && \
    docker-php-ext-install zip

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# PHP konfigurace
COPY etc/php-uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# bash aliases for all users
COPY etc/bash.aliases /etc/bash.aliases
RUN echo '\n. /etc/bash.aliases' >> /etc/bash.bashrc

# align www-data UID/GID with host user (1000) so mounted volumes are writable
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# allow www-data to use git in mounted volume and have writable composer cache
RUN git config --system --add safe.directory /var/www/html && \
    mkdir -p /var/www/.cache/composer && \
    chown -R www-data:www-data /var/www/.cache

# allow mod rewrite for Apache
RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

EXPOSE 80

COPY etc/docker-startup.sh /usr/local/bin/docker-startup.sh
RUN chmod +x /usr/local/bin/docker-startup.sh
CMD ["docker-startup.sh"]
