FROM php:8.3.10

# Install dependencies
RUN apt-get update -y && apt-get install -y openssl zip unzip git libpq-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Set working directory
WORKDIR /app

# Copy all source code into the container
COPY . /app

# Install PHP dependencies
RUN composer install

# Expose the Laravel dev server port
EXPOSE 8002

# Default command to run Laravel
CMD php artisan serve --host=0.0.0.0 --port=8002
