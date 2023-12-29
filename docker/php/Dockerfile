FROM php:8.1-fpm

RUN apt-get update \
    && apt-get install -y \
        gcc \
        make \
        autoconf \
        libc-dev \
        libzip-dev \
        unzip \
        pkg-config \
        libssl-dev
    
RUN pecl install \
        xdebug

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

#RUN chmod -R 755 ~/.composer/

WORKDIR /var/www

RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-install zip && \
    docker-php-ext-enable pdo_mysql &&\
    docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli