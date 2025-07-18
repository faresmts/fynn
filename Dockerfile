FROM php:8.4-fpm-alpine AS base

WORKDIR /var/www/html

RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    jpeg-dev \
    freetype-dev \
    oniguruma-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml

FROM node:20-alpine AS node_stage

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm install

FROM composer:2 AS composer_stage

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

FROM base AS production

COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

COPY --from=composer_stage /app/vendor /var/www/html/vendor

COPY --from=node_stage /app/node_modules /var/www/html/node_modules

COPY . .
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
