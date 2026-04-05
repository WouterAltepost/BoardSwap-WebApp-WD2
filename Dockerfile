FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader
COPY backend/ ./

# Point Apache document root to public/
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# Allow .htaccess rewrites
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

EXPOSE 8080

# Swap Apache's default port 80 for Railway's PORT at runtime, then start
CMD sed -i "s/Listen 80/Listen ${PORT:-8080}/" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT:-8080}/" /etc/apache2/sites-available/000-default.conf \
    && apache2-foreground
