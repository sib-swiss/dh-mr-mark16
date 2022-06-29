FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/htdocs/public

RUN apt-get update && apt-get install -y \
    wget \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install -j$(nproc) exif
RUN docker-php-ext-enable exif

RUN docker-php-ext-install -j$(nproc) opcache
RUN docker-php-ext-enable opcache

RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-enable zip

RUN wget -q https://getcomposer.org/installer -O composer-setup.php
RUN php composer-setup.php --quiet && rm composer-setup.php && mv composer.phar /usr/local/bin/composer && composer -V
RUN printf "\n\nDetected active directory: $(pwd)\n" && \
    printf "\nOnce the container is started, please run the following command:\n\ndocker exec -it \$(docker ps --filter 'name=manuscript' -q | head -1 -) bash -c 'composer install -d $(pwd)/htdocs && composer update -d $(pwd)/htdocs'\n\n\n"

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf