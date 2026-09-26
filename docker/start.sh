#!/bin/sh

set -e

echo "========================================"
echo "Running Laravel database migrations..."
echo "========================================"

php artisan migrate --force

echo "========================================"
echo "Starting Supervisor..."
echo "========================================"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf