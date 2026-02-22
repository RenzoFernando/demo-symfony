FROM php:8.4-fpm-alpine

RUN apk add --no-cache git unzip icu-dev oniguruma-dev libzip-dev \
  && docker-php-ext-install intl pdo pdo_mysql opcache zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 app && adduser -G app -g app -s /bin/sh -D -u 1000 app
USER app