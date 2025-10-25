FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

# Copy only composer files first
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy full application
COPY . .

# Run Laravel build commands
RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan route:cache

# Environment configuration
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
