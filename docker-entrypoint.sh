#!/bin/sh
set -e

PORT=${PORT:-80}

echo "==> Configuring nginx to listen on port $PORT..."
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/nginx.conf

echo "==> Checking Vue SPA files..."
ls -la /var/www/spa/ || echo "ERROR: /var/www/spa does not exist or is empty"

echo "==> Testing nginx config..."
nginx -t

echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Waiting for PHP-FPM..."
sleep 2

echo "==> Checking PHP-FPM is listening on 9000..."
netstat -tlnp 2>/dev/null | grep 9000 || ss -tlnp | grep 9000 || echo "WARNING: nothing on port 9000"

echo "==> Starting Nginx..."
exec nginx -g "daemon off;"
