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

# --- Configure Apache DocumentRoot ---
RUN sed -i "s|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<Directory \/var\/www\/html>/c\<Directory /var/www/html/public>" /etc/apache2/sites-available/000-default.conf

# --- Enable mod_rewrite (required for clean URLs) ---
RUN a2enmod rewrite

# --- Add custom Apache rewrite rules ---
RUN echo '\
<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride None\n\
    Require all granted\n\
    RewriteEngine On\n\
    # API routing: everything under /api/ -> api.php\n\
    RewriteCond %{REQUEST_URI} ^/api/\n\
    RewriteRule ^api/(.*)$ /api.php [L,QSA]\n\
    # UI routing: if the requested file or directory does not exist -> index.php\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^ index.php [L]\n\
</Directory>\n' \
> /etc/apache2/conf-available/docparser-rewrites.conf \
 && a2enconf docparser-rewrites

# --- Ensure Apache runs in foreground ---
CMD ["apache2-foreground"]
