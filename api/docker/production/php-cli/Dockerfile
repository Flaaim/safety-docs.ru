FROM php:8.1-cli-alpine AS builder

RUN apk add --no-cache mysql-dev \
    && docker-php-ext-install pdo_mysql mysqli

RUN apk add --no-cache unzip

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --optimize-autoloader \
     && rm -rf /root/.composer/cache

### CLI ###

FROM php:8.1-cli-alpine

RUN apk add --no-cache mysql-dev bash coreutils \
    && docker-php-ext-install pdo_mysql mysqli

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d

COPY ./docker/common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

WORKDIR /app

COPY --from=builder /app ./

COPY ./ ./

RUN chown www-data:www-data ./var -R
