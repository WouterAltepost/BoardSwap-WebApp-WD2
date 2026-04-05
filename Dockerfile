FROM php:8.2-cli-alpine
RUN docker-php-ext-install pdo pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts
COPY backend/ ./
EXPOSE 8080
CMD php -S 0.0.0.0:${PORT:-8080} -t public/ public/index.php
