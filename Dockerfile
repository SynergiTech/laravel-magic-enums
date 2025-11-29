ARG PHP_VERSION=8.2
FROM php:$PHP_VERSION-cli-alpine

RUN apk add git zip unzip autoconf make g++

# apparently newer xdebug needs these now?
RUN apk add --update linux-headers

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /package

RUN adduser -D -g '' dev

RUN chown dev -R /package

USER dev

COPY --chown=dev composer.json ./

ARG LARAVEL=9
# Currently Laravel 9 requires audit.block-insecure to be false
RUN if [ $LARAVEL = 9 ]; then composer config audit.block-insecure false; fi
RUN composer require laravel/framework ^$LARAVEL.0

COPY --chown=dev . .

RUN composer test
