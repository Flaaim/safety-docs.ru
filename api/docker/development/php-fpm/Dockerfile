FROM php:8.1-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions intl

RUN apk add --no-cache mysql-dev bash coreutils autoconf g++ make \
    && docker-php-ext-install pdo_mysql mysqli \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf g++ make

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

WORKDIR /app
