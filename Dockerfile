# Use PHP 7.4 with Apache
FROM php:7.4-apache

# Set working directory
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure zip
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mode_rewrite
RUN a2enmod rewrite

# Set the document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update the Apache default site to point to the public folder
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides (needed for Laravel routing)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Copy project files to the container
COPY . /var/www/html/

# Set correct file permissions for Laravel
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install PHP dependencies with Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node.js dependencies (fix permission issue with --unsafe-perm)
RUN npm install --unsafe-perm

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

