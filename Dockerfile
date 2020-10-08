FROM php:7.3.6-fpm-alpine3.9
# Install open ssl, bash e client mysql
RUN apk add --no-cache openssl \
    mysql-client \
    nodejs \
    npm \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

RUN docker-php-ext-install pdo pdo_mysql

# Manipulação de imagens
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include --with-png-dir=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
RUN rm -rf /var/www/html
COPY . /var/www
RUN  composer install \
    && php artisan config:cache \
    && chmod -R 775 storage

RUN ln -s public html

EXPOSE 9000
ENTRYPOINT [ "php-fpm" ]
