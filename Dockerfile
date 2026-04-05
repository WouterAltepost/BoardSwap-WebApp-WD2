# Stage 1 — Build Vue frontend
FROM node:20-alpine AS frontend-build
WORKDIR /build

ARG VITE_API_URL=/api
ENV VITE_API_URL=$VITE_API_URL

COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci
COPY frontend/ ./
RUN npm run build-only

# Stage 2 — PHP-FPM + Nginx production image
FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader
COPY backend/ ./

RUN echo '[www]' > /usr/local/etc/php-fpm.d/zz-listen.conf && \
    echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/zz-listen.conf

COPY --from=frontend-build /build/dist /var/www/spa
COPY nginx.railway.conf /etc/nginx/nginx.conf.template
COPY docker-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080

CMD ["/entrypoint.sh"]
