FROM php:8.1-fpm-alpine AS builder

RUN apk add --no-cache mysql-dev \
    && docker-php-ext-install pdo_mysql mysqli

RUN apk add --no-cache unzip

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --optimize-autoloader \
     && rm -rf /root/.composer/cache

### FPM ###
FROM php:8.1-fpm-alpine


RUN apk add --no-cache mysql-dev \
    && docker-php-ext-install pdo_mysql mysqli


RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY --from=builder /app ./
COPY ./ ./

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /app/var

USER www-data