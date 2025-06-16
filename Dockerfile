FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies needed for PHP extensions and Node.js
RUN apk add --no-cache \
    unzip \
    git \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    g++ \
    make \
    bash \
    nodejs \
    npm \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    pkgconf

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        bcmath \
        gd \
        exif \
        mbstring \
        fileinfo \
        intl \
        xml \
        zip \
    && docker-php-ext-enable pdo_pgsql gd intl zip mbstring

# Remove build dependencies to keep image size small
RUN apk del --no-cache \
    g++ \
    make \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    pkgconf \
    unzip \
    git

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy entire source before installing dependencies (artisan must exist)
COPY . .

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build frontend assets
RUN npm install \
    && npm run build

# Set permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy the entrypoint script and make it executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Use the entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Default command
CMD ["php-fpm"]
