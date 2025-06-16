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
    npm

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
        json \
        openssl \
        tokenizer \
        xml \
        zip \
    # Remove development headers and build tools to keep the image small
    && apk del --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev g++ make

# Copy Composer from its official image for efficiency
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy only composer.json and package.json/yarn.lock first to leverage Docker cache
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Install PHP dependencies (only production dependencies)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build frontend assets
RUN npm install \
    && npm run build

# Copy the rest of the application source code
COPY . .

# Run Laravel optimization commands for production that don't depend on DB/APP_KEY
RUN php artisan cache:clear \
    && php artisan view:cache \
    && php artisan route:cache \
    && php artisan filament:cache-components \
    && php artisan filament:optimize \
    && php artisan icons:cache \
    && php artisan event:cache \
    && php artisan config:cache \
    && php artisan optimize

# Set appropriate permissions for Laravel's storage and cache directories
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy the entrypoint script and make it executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Set the entrypoint to our custom script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
# The CMD will be passed as arguments to the entrypoint script
CMD ["php-fpm"]
