FROM php:8.1-fpm-alpine

# Install required packages
RUN apk --update --no-cache add bash git autoconf gcc make g++ \
        && pecl install pcov \
        && docker-php-ext-enable pcov

#Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www

CMD php-fpm

EXPOSE 9000
