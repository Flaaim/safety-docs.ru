FROM php:8.1-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions intl

RUN apk add --no-cache mysql-dev \
    && docker-php-ext-install pdo_mysql mysqli

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

WORKDIR /app
