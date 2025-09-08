# Я предпочитаю указывать минорные версии, чтобы гарантировать согласованность сервисов
FROM php:8.4.7-fpm

# Моя стандартная сборка Dockerfile

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    librabbitmq-dev \
    libsodium-dev
    # supervisor

RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    pdo \
    pdo_pgsql \
    gd \
    opcache \
    intl \
    zip \
    soap \
    sockets \
    calendar

# Если понадобится RabbitMQ и/или Redis
# RUN pecl install redis && docker-php-ext-enable redis
# RUN pecl install amqp && docker-php-ext-enable amqp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get autoremove -y

WORKDIR /var/www/app
