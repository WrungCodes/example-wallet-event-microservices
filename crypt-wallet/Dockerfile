# Base image
FROM php:7.3-apache

WORKDIR /var/www/html/

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/html/

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    default-mysql-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libxml2-dev \
    supervisor 

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    mbstring \
    opcache \
    pdo_mysql \
    soap

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN curl -O https://getcomposer.org/download/1.9.0/composer.phar
RUN mv ./composer.phar /usr/local/bin/composer
RUN chmod 755 /usr/local/bin/composer

# Copy existing application directory contents
COPY . /var/www/html/

WORKDIR /var/www/html

RUN composer install  \
    --ignore-platform-reqs \
    --no-ansi \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist

RUN chown -R www-data:www-data /var/www/html/*

# # Update web root to public
# # See: https://hub.docker.com/_/php#changing-documentroot-or-other-apache-configuration
# ENV APACHE_DOCUMENT_ROOT /var/www/html/public
# RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
# RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY apache/apache.conf /etc/apache2/sites-available/000-default.conf
COPY apache/ports.conf /etc/apache2/ports.conf


# Enable mod_rewrite
RUN a2enmod rewrite
