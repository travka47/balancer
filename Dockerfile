FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    gnupg \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev

RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /app
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

EXPOSE 8000
