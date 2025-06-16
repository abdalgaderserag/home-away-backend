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


# Generate APP_KEY if it's not set.
# This is mainly for local development. In production, provide APP_KEY via ENV var.
if [ ! -f .env ] || ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
  echo "Generating application key..."
  php artisan key:generate
fi


# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

php artisan optimize:clear
php artisan optimize
php artisan config:cache
php artisan view:cache
php artisan route:cache
php artisan icons:cache
php artisan filament:optimize
php artisan event:cache

# Seed the database (optional, run only if you want initial data)
echo "Seeding database..."
php artisan db:seed --force
# Set permissions for Laravel
chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
# --- Execute the original CMD ---
# This ensures that the main process (php-fpm) starts after setup.
echo "Starting PHP-FPM..."
exec "$@"
