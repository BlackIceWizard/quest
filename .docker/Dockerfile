FROM allansimon/php8-fpm-postgresql

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
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install intl
RUN docker-php-ext-install exif
RUN docker-php-ext-install opcache

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN wget https://get.symfony.com/cli/installer -O ./symfony
RUN chmod +x ./symfony
RUN ./symfony --install-dir=/usr/local/bin
RUN rm ./symfony