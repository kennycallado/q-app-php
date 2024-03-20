FROM php:8.2-apache

ENV ENVIRONMENT="production"

# Install intl to support multi-language
RUN apt-get update && apt-get install -y libicu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#

RUN a2enmod rewrite

COPY . /var/www/html
