FROM php:8.0-fpm

MAINTAINER Vladimir Cheskidov <black.ice.wizard@gmail.com>

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libicu-dev \
        libpq-dev \
        wget \
        git \
        libxpm-dev \
        libvpx-dev \
        curl \
        unzip

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install -j$(nproc) bcmath
RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install -j$(nproc) pgsql
RUN docker-php-ext-install -j$(nproc) pdo_pgsql
RUN docker-php-ext-install -j$(nproc) exif
RUN docker-php-ext-install -j$(nproc) opcache

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN wget https://get.symfony.com/cli/installer -O ./symfony
RUN chmod +x ./symfony
RUN ./symfony --install-dir=/usr/local/bin
RUN rm ./symfony