# Stage 1: Build Vite assets
FROM node:22-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js ./
RUN npm run build

# Stage 2: PHP runtime
FROM php:8.3-apache AS runtime

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libsqlite3-dev \
    curl \
    && docker-php-ext-install pdo pdo_sqlite bcmath zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Apache vhost
COPY docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# OPcache config
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies (cache-friendly: copy manifests first)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction

# Copy application source
COPY . .

# Copy built frontend assets
COPY --from=node-builder /app/public/build public/build

# Bake caches into the image
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan optimize

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache database

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
