# Use official PHP 8.2 FPM base image with Alpine
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system and PHP build dependencies
RUN apk add --no-cache \
    unzip \
    git \
    bash \
    nodejs \
    npm \
    g++ \
    make \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    pkgconf

# Configure GD with JPEG and FreeType support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pgsql \
    bcmath \
    gd \
    exif \
    mbstring \
    intl \
    xml \
    zip

# Clean up build tools and development headers
RUN apk del --no-cache \
    g++ \
    make \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    pkgconf \
    && rm -rf /var/cache/apk/*

# Reinstall runtime libraries needed for compiled PHP extensions
RUN apk add --no-cache \
    libpng \
    libjpeg-turbo \
    freetype \
    libpq \
    libzip

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy all source code first
COPY . .

# Then install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build frontend assets
RUN npm install && npm run build

# Copy application source code
COPY . .

# Set correct permissions for Laravel storage paths
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy custom entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose PHP-FPM port
EXPOSE 9000

# Set custom entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Default command to start PHP-FPM
CMD ["php-fpm"]
