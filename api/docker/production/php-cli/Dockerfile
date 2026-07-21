FROM php:8.1-cli-alpine AS builder

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip intl redis

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY ./composer.json ./composer.lock ./
COPY ./ ./

RUN composer install --no-dev --prefer-dist --no-progress --classmap-authoritative \
    && rm -rf /root/.composer/cache \

### CLI ###

FROM php:8.1-cli-alpine

RUN apk add --no-cache bash

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql mysqli zip redis intl opcache

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d

COPY ./docker/common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

WORKDIR /app

COPY ./ ./
COPY --from=builder /app/vendor ./vendor

RUN mkdir -p var/cache var/log public/templates \
    && chown -R www-data:www-data ./var ./public/templates

USER www-data