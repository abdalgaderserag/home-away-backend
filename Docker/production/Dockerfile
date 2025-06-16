FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    unzip \
    git \
    bash \
    nodejs \
    npm \
    icu-libs \
    oniguruma \
    postgresql-libs \
    libpng \
    libjpeg-turbo \
    freetype \
    libxml2 \
    libzip \

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pgsql \
    bcmath \
    gd \
    exif \
    mbstring \
    intl \
    xml \
    zip \
    redis

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY composer.json composer.lock ./


RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

RUN npm ci --prefer-offline --no-audit && npm run build # Use npm ci for clean installs

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["php-fpm"]