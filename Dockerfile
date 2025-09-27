# Use the official PHP 8.3 FPM image (Alpine for smaller size) to match composer.lock requirements
FROM php:8.3-fpm-alpine

# Set working directory inside the container
WORKDIR /var/www/html

# 1. Install System Dependencies
# We use apk (Alpine's package manager) to install necessary tools and libraries.
RUN apk update && apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    freetype-dev \
    libpq-dev \
    librdkafka-dev \
    $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

# 2. Install and Enable PHP Extensions
# Configure and install GD with necessary support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip gd

# Install and enable the php-rdkafka extension using PECL
# FIX: Added 'pecl channel-update' to resolve 'No releases available' error.
RUN pecl channel-update pecl.php.net \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka

# 3. Install Composer (PHP Dependency Manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copy Application Files
# We assume the entire Laravel project is in the same directory as the Dockerfile
COPY . /var/www/html

# 5. Composer Install (Ensure Composer dependencies are met)
# Use the --no-dev and --optimize-autoloader flags for production-like builds
RUN composer install --no-dev --optimize-autoloader

# 6. Set proper permissions (Required for Laravel to write logs/cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Expose PHP-FPM port
EXPOSE 9000

# 8. Switch to the non-root user for security (Best Practice)
USER www-data

# The default command runs php-fpm
CMD ["php-fpm"]
