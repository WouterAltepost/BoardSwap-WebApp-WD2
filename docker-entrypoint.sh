#!/bin/sh
set -e

PORT="${PORT:-8080}"
echo "Starting on port $PORT"

# Inject PORT into nginx config
sed "s/NGINX_PORT/$PORT/" /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g "daemon off;"
