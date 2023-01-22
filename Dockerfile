FROM php:8.2-cli

# Install dependencies
RUN apt-get update \
    && apt-get install -y \
    mariadb-client \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Copy the application code
COPY . /var/www/ALED_API

# Set the working directory
WORKDIR /var/www/ALED_API

# Install composer dependencies
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer install --no-dev -d /var/www/ALED_API

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/ALED_API"]
