FROM php:8.2-cli-alpine
RUN docker-php-ext-install pdo pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader
COPY backend/ ./
RUN php -r "require 'vendor/autoload.php'; echo 'Autoloader OK\n';"
EXPOSE 8080
CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-8080} -t public/ public/index.php 2>&1"]
