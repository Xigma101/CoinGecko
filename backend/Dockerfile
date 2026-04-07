FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files and install dependencies
# This layer is cached unless composer files change
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-autoloader

# Copy application code
COPY . .

# Finalize autoloader
RUN composer dump-autoload --optimize

EXPOSE 8000

# Use entrypoint script to handle setup on container start
CMD php artisan key:generate --force --no-interaction 2>/dev/null; \
    php artisan serve --host=0.0.0.0 --port=8000
