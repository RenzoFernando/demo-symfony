FROM php:8.4-fpm-alpine

RUN apk add --no-cache git unzip icu-dev oniguruma-dev libzip-dev $PHPIZE_DEPS \
  && docker-php-ext-install intl pdo pdo_mysql opcache zip \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 app && adduser -G app -g app -s /bin/sh -D -u 1000 app
USER app