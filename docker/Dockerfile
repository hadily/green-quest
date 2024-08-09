# Use the official PHP 8.2 Apache image as the base image
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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the Symfony project files into the container
COPY app /var/www/html

# Create necessary directories
RUN mkdir -p /var/www/html/var /var/www/html/vendor

# Set the correct permissions
RUN chown -R www-data:www-data /var/www/html/var \
    && chown -R www-data:www-data /var/www/html/vendor

# Set environment variable to allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set the DATABASE_URL environment variable
ENV DATABASE_URL="mysql://root@host.docker.internal:3306/expDouce"

# Install Symfony CLI tool
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Install Symfony dependencies
RUN composer install --no-interaction --optimize-autoloader --prefer-dist

# Expose port 8000 for Symfony CLI server
EXPOSE 8000

# Command to start Symfony CLI server on port 8000
CMD ["symfony", "server:start", "--port=8000", "--no-tls"]
