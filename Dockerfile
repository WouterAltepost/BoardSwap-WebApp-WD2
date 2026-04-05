FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader
COPY backend/ ./

# Configure PHP-FPM to listen on 127.0.0.1:9000
RUN echo '[www]' > /usr/local/etc/php-fpm.d/zz-listen.conf && \
    echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/zz-listen.conf

EXPOSE 8080

# Inline nginx config + start both nginx and php-fpm
CMD echo "worker_processes 1;" > /etc/nginx/nginx.conf && \
    echo "events { worker_connections 128; }" >> /etc/nginx/nginx.conf && \
    echo "http {" >> /etc/nginx/nginx.conf && \
    echo "  include /etc/nginx/mime.types;" >> /etc/nginx/nginx.conf && \
    echo "  server {" >> /etc/nginx/nginx.conf && \
    echo "    listen ${PORT:-8080};" >> /etc/nginx/nginx.conf && \
    echo "    root /app/public;" >> /etc/nginx/nginx.conf && \
    echo "    index index.php;" >> /etc/nginx/nginx.conf && \
    echo "    location / { try_files \$uri /index.php\$is_args\$args; }" >> /etc/nginx/nginx.conf && \
    echo "    location ~ \\.php\$ {" >> /etc/nginx/nginx.conf && \
    echo "      fastcgi_pass 127.0.0.1:9000;" >> /etc/nginx/nginx.conf && \
    echo "      fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;" >> /etc/nginx/nginx.conf && \
    echo "      include fastcgi_params;" >> /etc/nginx/nginx.conf && \
    echo "    }" >> /etc/nginx/nginx.conf && \
    echo "  }" >> /etc/nginx/nginx.conf && \
    echo "}" >> /etc/nginx/nginx.conf && \
    php-fpm -D && \
    nginx -g "daemon off;"
