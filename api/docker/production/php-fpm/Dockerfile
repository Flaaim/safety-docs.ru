FROM php:8.1-fpm-alpine AS builder

RUN apk add --no-cache unzip

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --optimize-autoloader --ignore-platform-req=ext-intl --ignore-platform-req=ext-redis && rm -rf /root/.composer/cache


### FPM ###
FROM php:8.1-fpm-alpine

RUN apk add --no-cache mysql-dev autoconf g++ make \
    && docker-php-ext-install pdo_mysql mysqli \
    && pecl install redis \
    && docker-php-ext-enable redis opcache \
    && apk del autoconf g++ make

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions intl

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY --from=builder /app ./
COPY ./ ./

RUN mkdir -p var/cache var/log public/templates \
    && chown -R www-data:www-data ./var ./public/templates

USER www-data