FROM php:8.2-fpm

RUN docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader
COPY backend/ ./
