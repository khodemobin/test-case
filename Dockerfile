FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /app

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]