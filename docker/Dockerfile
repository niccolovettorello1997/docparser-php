# --- Base image ---
FROM php:8.3-apache

# --- Install system dependencies & Composer ---
RUN apt-get update && apt-get install -y \
        curl \
        unzip \
        git \
        lsof \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

# --- Install and configure Xdebug ---
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && { \
        echo "zend_extension=xdebug.so"; \
        echo "xdebug.mode=coverage,debug"; \
        echo "xdebug.start_with_request=yes"; \
        echo "xdebug.client_host=host.docker.internal"; \
        echo "xdebug.client_port=9003"; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# --- Enable Apache rewrite module for clean URLs ---
RUN a2enmod rewrite

# --- Configure Apache to serve from /public ---
RUN sed -i \
        -e 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' \
        -e '/<Directory \/var\/www\/html>/c\<Directory /var/www/html/public>' \
        /etc/apache2/sites-available/000-default.conf

# --- Copy .htaccess for API routing ---
COPY public/.htaccess /var/www/html/public/.htaccess

# --- Set working directory ---
WORKDIR /var/www/html

# --- Adjust permissions ---
RUN chown -R www-data:www-data /var/www/html

# --- Run Apache in foreground ---
CMD ["apache2-foreground"]
