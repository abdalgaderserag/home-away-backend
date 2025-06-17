#!/bin/sh

# --- Wait for the database to be ready (IMPORTANT for migrations) ---
# You might need to adjust 'db' to your database service name in docker-compose.yml
# and the port if it's not 5432 for PostgreSQL.
echo "Waiting for database to be ready..."
until nc -z db 5432; do
  echo "Database is unavailable - sleeping"
  sleep 2
done
echo "Database is up and running!"

# Set permissions for Laravel
chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
# --- Execute the original CMD ---
# This ensures that the main process (php-fpm) starts after setup.
echo "Starting PHP-FPM..."
exec "$@"
