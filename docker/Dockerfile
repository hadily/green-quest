# Use the official PHP image as the base image
FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Install necessary system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the existing application directory contents
COPY app /var/www/html

# Create necessary directories
RUN mkdir -p /var/www/html/var /var/www/html/vendor

# Set the correct permissions
RUN chown -R www-data:www-data /var/www/html/var \
    && chown -R www-data:www-data /var/www/html/vendor

# Install Symfony CLI tool
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set environment variable to allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Symfony dependencies
RUN composer install

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
