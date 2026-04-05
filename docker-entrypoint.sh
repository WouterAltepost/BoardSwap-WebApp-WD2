#!/bin/sh
set -e

PORT=${PORT:-80}

echo "==> Configuring nginx to listen on port $PORT..."
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/nginx.conf

echo "==> Testing nginx config..."
nginx -t

echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Waiting for PHP-FPM to be ready..."
sleep 2

echo "==> Starting Nginx..."
exec nginx -g "daemon off;"
