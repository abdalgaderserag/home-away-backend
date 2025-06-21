#!/bin/bash

echo "🚀 Starting Production Deployment..."

# Check if .env.production exists
if [ ! -f ".env.production" ]; then
    echo "❌ .env.production file not found. Please create it from env.production.example"
    exit 1
fi

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Stop existing containers
echo "🛑 Stopping existing containers..."
docker-compose down

# Build and start services
echo "📦 Building and starting production services..."
docker-compose up -d --build

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 15

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose exec app php artisan migrate --force

# Clear and cache configurations
echo "⚡ Optimizing for production..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set permissions
echo "🔐 Setting proper permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache

# Health check
echo "🏥 Performing health check..."
if curl -f http://localhost/health > /dev/null 2>&1; then
    echo "✅ Application is healthy!"
else
    echo "⚠️ Health check failed. Check logs with: docker-compose logs app"
fi

echo "✅ Production deployment completed!"
echo ""
echo "🌐 Application: https://your-domain.com"
echo "📊 Monitoring: Check logs and metrics"
echo ""
echo "📝 Useful commands:"
echo "  docker-compose logs -f app    # View app logs"
echo "  docker-compose exec app bash  # Access app container"
echo "  docker-compose down           # Stop all services"
echo "  docker-compose restart app    # Restart application" 