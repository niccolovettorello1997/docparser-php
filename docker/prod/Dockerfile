FROM php:8.3-apache

# --- Install dependencies ---
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# --- Composer ---
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- Set working directory ---
WORKDIR /var/www/html

# --- Copy project files ---
COPY . .

# --- Install PHP dependencies ---
RUN composer install --no-interaction --optimize-autoloader --no-dev

# --- Set permissions ---
RUN chown -R www-data:www-data /var/www/html

# --- Adjust Apache port to Render's $PORT ---
ENV APACHE_PORT=${PORT:-80}
RUN sed -i "s/Listen 80/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
RUN sed -i "s|<VirtualHost \*:80>|<VirtualHost *:${APACHE_PORT}>|" /etc/apache2/sites-available/000-default.conf

# --- Ensure Apache serves public/ ---
RUN sed -i "s|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<Directory \/var\/www\/html>/c\<Directory /var/www/html/public>" /etc/apache2/sites-available/000-default.conf

# --- Ensure Apache runs in foreground ---
CMD ["apache2-foreground"]
