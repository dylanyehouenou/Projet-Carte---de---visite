FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libexif-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Dev: code + vendor/ are mounted via volume in docker-compose.yml
# For prod: COPY . . && composer install --no-interaction --optimize-autoloader --no-dev

EXPOSE 9000
CMD ["php-fpm"]
