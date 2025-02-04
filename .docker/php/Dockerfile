FROM php:8.3-fpm

WORKDIR /var/www

RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gettext intl pdo_mysql gd pdo mbstring zip exif bcmath pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.7.2 /usr/bin/composer /usr/bin/composer

COPY . /var/www
RUN chmod -R 775 /var/www && chown -R www-data:www-data /var/www

USER www-data

RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]
