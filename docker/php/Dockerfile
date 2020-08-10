FROM php:7.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends apt-utils
RUN apt-get update
RUN apt-get install libicu-dev libzip-dev -y
RUN apt-get install libfreetype6-dev libxpm-dev libvpx-dev libpng-dev libjpeg-dev -y

RUN docker-php-ext-configure gd \
    --with-freetype-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
    --with-xpm-dir=/usr/include/ \
    --with-png-dir=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install json intl pdo_mysql zip

# xDebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# zip
RUN apt-get install zip -y

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
