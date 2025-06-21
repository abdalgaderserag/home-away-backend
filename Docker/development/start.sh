#!/bin/bash

echo "🚀 Starting Development Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Build and start services
echo "📦 Building and starting services..."
docker-compose up -d --build

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose exec app php artisan migrate --force

# Clear caches
echo "🧹 Clearing application caches..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear

# Install dependencies if needed
echo "📦 Installing dependencies..."
docker-compose exec app composer install --no-interaction

# Set permissions
echo "🔐 Setting proper permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "✅ Development environment is ready!"
echo ""
echo "🌐 Application: http://localhost"
echo "📧 Mailpit: http://localhost:8025"
echo "🗄️ Database: localhost:5432"
echo "🔴 Redis: localhost:6379"
echo ""
echo "📝 Useful commands:"
echo "  docker-compose logs -f app    # View app logs"
echo "  docker-compose exec app bash  # Access app container"
echo "  docker-compose down           # Stop all services" 