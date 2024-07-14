FROM php:8.1-fpm-alpine

RUN apk --update --no-cache add git autoconf gcc make g++ \
        && pecl install pcov

#Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www

CMD php-fpm

EXPOSE 9000
