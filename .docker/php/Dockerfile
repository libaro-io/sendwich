FROM node:latest AS node
FROM php:8-apache

# Install nodejs
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN npm install -g npx

ARG USER=dev
ARG SSH_PRIVATE_KEY

ENV APACHE_DOCUMENT_ROOT=/var/www/public

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
RUN pecl install imagick
RUN docker-php-ext-enable imagick

# Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER

# Add ssh private key into container
RUN mkdir /home/$USER/.ssh
RUN echo "${SSH_PRIVATE_KEY}" > /home/$USER/.ssh/id_rsa
RUN chmod 600 /home/$USER/.ssh/id_rsa
RUN ssh-keyscan bitbucket.org >> /home/$USER/.ssh/known_hosts
RUN chown -R $USER: /home/$USER

# Set working directory
WORKDIR /var/www
