FROM php:8.3-fpm-alpine

WORKDIR /var/www/html


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


RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip gd


RUN pecl channel-update pecl.php.net \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


COPY . /var/www/html


RUN composer install --no-dev --optimize-autoloader
# RUN composer install --optimize-autoloader /// production

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
