# Docker Setup Documentation

## Overview

This project includes separate Docker configurations for development and production environments, with optimized settings for each use case.

## 🏗️ Architecture

### Development Environment (Root Directory)
- **Purpose**: Local development with hot reloading and debugging
- **Services**: App, Nginx, PostgreSQL, Redis, Mailpit
- **Features**: Volume mounting, development tools, debugging enabled

### Production Environment (`/docker/production`)
- **Purpose**: Production deployment with security and performance optimizations
- **Services**: App, Nginx (SSL), PostgreSQL, Redis, Redis Sentinel
- **Features**: Multi-stage builds, security hardening, performance tuning

## 🚀 Quick Start

### Development Environment

1. **Start Development Environment:**
   ```bash
   # From project root
   chmod +x docker/development/start.sh
   ./docker/development/start.sh
   ```

2. **Manual Start:**
   ```bash
   docker-compose up -d --build
   ```

3. **Access Services:**
   - Application: http://localhost
   - Mailpit: http://localhost:8025
   - Database: localhost:5432
   - Redis: localhost:6379

### Production Environment

1. **Setup Production Environment:**
   ```bash
   # Navigate to production directory
   cd docker/production
   
   # Copy and configure environment file
   cp env.production.example .env.production
   # Edit .env.production with your production values
   
   # Make deployment script executable
   chmod +x deploy.sh
   ```

2. **Deploy to Production:**
   ```bash
   ./deploy.sh
   ```

3. **Manual Deployment:**
   ```bash
   docker-compose up -d --build
   ```

## 📁 File Structure

```
├── docker-compose.yml              # Development environment
├── Dockerfile.dev                  # Development Dockerfile
├── docker/
│   ├── development/
│   │   ├── start.sh               # Development startup script
│   │   ├── php.ini                # Development PHP config
│   │   └── supervisord.conf       # Development supervisor config
│   └── production/
│       ├── docker-compose.yml     # Production environment
│       ├── Dockerfile             # Production Dockerfile
│       ├── deploy.sh              # Production deployment script
│       ├── env.production.example # Production env template
│       ├── php.ini                # Production PHP config
│       ├── supervisord.conf       # Production supervisor config
│       ├── nginx/
│       │   └── nginx.conf         # Production Nginx config
│       └── redis/
│           └── sentinel.conf      # Redis Sentinel config
```

## 🔧 Configuration Differences

### Development vs Production

| Feature | Development | Production |
|---------|-------------|------------|
| **PHP Settings** | Debug enabled, OPcache disabled | Debug disabled, OPcache enabled |
| **Error Display** | On | Off |
| **Memory Limit** | 512M | 256M |
| **File Uploads** | 100M | 50M |
| **SSL** | Disabled | Enabled with Let's Encrypt |
| **Queue Workers** | Single process | Multiple processes |
| **Caching** | Disabled | Enabled (config, route, view) |
| **Logging** | Debug level | Error level |
| **Security** | Basic | Enhanced (headers, SSL, etc.) |

## 🛠️ Development Commands

### Basic Operations
```bash
# Start development environment
docker-compose up -d

# View logs
docker-compose logs -f app

# Access container
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan migrate

# Install dependencies
docker-compose exec app composer install

# Clear caches
docker-compose exec app php artisan config:clear
```

### Testing
```bash
# Test notifications
docker-compose exec app php artisan test:notifications

# Test real-time system
docker-compose exec app php artisan test:realtime

# Test mail system
docker-compose exec app php artisan test:mail
```

### Database Operations
```bash
# Access database
docker-compose exec db psql -U laravel -d laravel

# Backup database
docker-compose exec db pg_dump -U laravel laravel > backup.sql

# Restore database
docker-compose exec -T db psql -U laravel -d laravel < backup.sql
```

## 🚀 Production Commands

### Deployment
```bash
# Deploy to production
cd docker/production
./deploy.sh

# Manual deployment
docker-compose up -d --build

# Zero-downtime deployment
docker-compose up -d --no-deps --build app
docker-compose restart nginx
```

### Monitoring
```bash
# View application logs
docker-compose logs -f app

# Check service status
docker-compose ps

# Monitor Redis
docker-compose exec redis redis-cli monitor

# Health check
curl https://your-domain.com/health
```

### Maintenance
```bash
# Backup production database
docker-compose exec db pg_dump -U laravel_user laravel_production > prod_backup.sql

# Update application
git pull origin main
docker-compose up -d --build app

# Clear production caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

## 🔐 Security Features

### Production Security
- **SSL/TLS**: Automatic HTTPS redirect
- **Security Headers**: XSS protection, CSRF protection, etc.
- **File Access**: Restricted access to sensitive files
- **Redis**: Password-protected Redis instance
- **Database**: Secure PostgreSQL configuration
- **Session Security**: Secure cookies, CSRF protection

### Environment Variables
- **Development**: Uses `.env` file with development defaults
- **Production**: Uses `.env.production` with secure production values
- **Secrets**: Database passwords, API keys, SSL certificates

## 📊 Performance Optimizations

### Development
- **Volume Mounting**: Live code changes without rebuilds
- **Debug Tools**: Xdebug, error reporting enabled
- **Hot Reloading**: Automatic service restarts

### Production
- **Multi-stage Builds**: Optimized image sizes
- **OPcache**: PHP bytecode caching
- **Nginx Caching**: Static file caching
- **Gzip Compression**: Reduced bandwidth usage
- **Queue Workers**: Multiple processes for better throughput

## 🔄 CI/CD Integration

### Development Workflow
```bash
# Feature development
git checkout -b feature/new-feature
# Make changes
docker-compose up -d
# Test changes
docker-compose exec app php artisan test
# Commit and push
git commit -m "Add new feature"
git push origin feature/new-feature
```

### Production Deployment
```bash
# Automated deployment (example)
git checkout main
git pull origin main
cd docker/production
./deploy.sh
```

## 🐛 Troubleshooting

### Common Issues

1. **Port Conflicts**
   ```bash
   # Check what's using the port
   lsof -i :80
   lsof -i :443
   
   # Stop conflicting services
   sudo systemctl stop apache2
   sudo systemctl stop nginx
   ```

2. **Permission Issues**
   ```bash
   # Fix storage permissions
   docker-compose exec app chown -R www-data:www-data /var/www/html/storage
   docker-compose exec app chmod -R 755 /var/www/html/storage
   ```

3. **Database Connection Issues**
   ```bash
   # Check database status
   docker-compose ps db
   
   # Test connection
   docker-compose exec app php artisan tinker
   DB::connection()->getPdo();
   ```

4. **Redis Connection Issues**
   ```bash
   # Check Redis status
   docker-compose exec redis redis-cli ping
   
   # Test Redis connection
   docker-compose exec app php artisan tinker
   Redis::ping();
   ```

### Debug Commands
```bash
# View all logs
docker-compose logs

# View specific service logs
docker-compose logs app
docker-compose logs nginx
docker-compose logs db
docker-compose logs redis

# Check container status
docker-compose ps

# Inspect container
docker-compose exec app php -i | grep redis
docker-compose exec app php -m | grep redis
```

## 📈 Monitoring and Logging

### Development Monitoring
- **Application Logs**: `docker-compose logs -f app`
- **Nginx Logs**: `docker-compose logs -f nginx`
- **Database Logs**: `docker-compose logs -f db`
- **Redis Logs**: `docker-compose logs -f redis`

### Production Monitoring
- **Health Checks**: `/health` endpoint
- **Application Metrics**: Laravel Telescope (if enabled)
- **System Metrics**: Docker stats
- **Error Tracking**: Sentry integration (optional)

## 🔄 Updates and Maintenance

### Updating Dependencies
```bash
# Update Composer dependencies
docker-compose exec app composer update

# Update NPM dependencies
docker-compose exec app npm update

# Rebuild containers
docker-compose up -d --build
```

### Database Migrations
```bash
# Development
docker-compose exec app php artisan migrate

# Production
docker-compose exec app php artisan migrate --force
```

### Cache Management
```bash
# Clear all caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear

# Cache for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Redis Documentation](https://redis.io/documentation)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/) 