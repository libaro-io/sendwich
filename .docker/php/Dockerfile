FROM node:16.19 AS node
FROM php:8.3-apache

ARG USER=dev

ENV APACHE_DOCUMENT_ROOT=/var/www/public

# Install nodejs
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN npm install -g npx

# Set document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable rewrite module
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libmagickwand-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install intl mysqli pdo pdo_mysql mbstring exif pcntl bcmath gd zip soap

# Install imagick extension
# Update the package list and install git
RUN apt-get update && \
    apt-get install -y git && \
    # Clone the Imagick PHP extension source code
    git clone https://github.com/Imagick/imagick.git --depth 1 /tmp/imagick && \
    cd /tmp/imagick && \
    # Fetch the latest master and switch to it
    git fetch origin master && \
    git switch master && \
    # Prepare for PHP extension installation
    phpize && \
    ./configure && \
    make && \
    make install && \
    # Clean up the installed packages to reduce image size
    apt-get remove -y git && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    # Enable the Imagick PHP extension
    docker-php-ext-enable imagick

# Install xdebug
RUN pecl install xdebug

# Instlal redis
RUN pecl install redis
RUN docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add scripts directory
COPY .docker/php/scripts /home/$USER/scripts
RUN mkdir /usr/local/etc/php/disabled
RUN chmod -R 777 /usr/local/etc/php
# Override files
COPY .docker/php/files/usr/local/etc/php/disabled/docker-php-ext-xdebug.ini /usr/local/etc/php/disabled/docker-php-ext-xdebug.ini

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER

# Set working directory
WORKDIR /var/www
