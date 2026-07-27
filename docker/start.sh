#!/bin/sh
set -e

echo "=== Running Laravel production startup tasks ==="

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link || true

# Cache configs, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting HTTP server on port ${PORT:-8000} ==="
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
