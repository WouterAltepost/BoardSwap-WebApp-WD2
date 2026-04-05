FROM php:8.2-cli-alpine

RUN apk add --no-cache unzip git \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader
COPY backend/ ./

EXPOSE 10000

CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-10000} -t public/ public/index.php"]
