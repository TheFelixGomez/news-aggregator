# Use a single stage build based on PHP
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
# Added nodejs and npm
RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    git \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    mysql-client \
    nodejs \
    npm \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Optimized layer caching.
# Copy dependency files first
 COPY composer.json composer.lock ./
# Install composer dependencies without running scripts, as artisan doesn't exist yet
RUN composer install --no-interaction --no-scripts --no-autoloader --no-dev

# Copy package.json files
COPY package.json package-lock.json ./
# Install npm dependencies
RUN npm install

# Copy the rest of the application
COPY . /var/www/html

# Add git safe directory to fix ownership warning
RUN git config --global --add safe.directory /var/www/html

# Clear ALL cached files from your local env
RUN rm -f bootstrap/cache/*.php

# Copy .env.example to .env to provide dummy env vars for build
# This prevents typed property errors when artisan boots the app
RUN cp .env.example .env

# Now that artisan exists, generate the autoloader and run scripts
RUN composer dump-autoload --optimize --no-dev

# Run the build *after* composer install and when PHP is available
RUN npm run build

# Clean up node_modules after build, we don't need them in production
RUN rm -rf node_modules

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]
