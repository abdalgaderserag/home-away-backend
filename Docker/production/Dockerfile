FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    postgresql-dev \
    supervisor \
    nginx \
    postgresql-client \
    redis \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies (production only)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Copy production-specific configurations
COPY docker/production/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create storage directories
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs

# Optimize for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Generate application key (will be overridden by environment)
RUN php artisan key:generate --no-interaction

# Expose port 9000
EXPOSE 9000

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]