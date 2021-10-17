FROM php:7.3-fpm-alpine3.11
# Permitie add user
RUN apk add --no-cache shadow
# Install open ssl, bash e client mysql
RUN apk add --no-cache openssl \
    bash \
    mysql-client \
    nodejs \
    npm \
    git \
    $PHPIZE_DEPS \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

RUN docker-php-ext-install pdo pdo_mysql bcmath sockets

RUN touch /root/.bashrc | echo "PS1='\w\$ '" >> /root/.bashrc

# Add XDevug
RUN pecl install -f xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_mode=req" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_log=/var/log/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.default_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_nesting_level=200" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=app" >> /usr/local/etc/php/conf.d/xdebug.ini

# Manipulação de imagens
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include --with-png-dir=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd

# Add dockerize na imagem, para fazer testes de validção
ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add cache in npm for improve performance
RUN npm config set cache /var/www/.npm-cache --global
# Add user in containner
RUN usermod -u 1000 www-data


WORKDIR /var/www

RUN rm -rf /var/www/html

RUN ln -s public html

#Add user
USER www-data

# COPY ./.docker/app/php.ini /usr/local/etc/php/conf.d/
# O que é raealizado aqui no Docker file pertenxe a imagem criada  e não ao volume compartilhado
# Devemos ter bem claro este conceito para as coisas funcionarem corretamente
# O copy abaixo é um exemplo, ele coloca os arquivos na imagem e não no volume compartilhado

# COPY . /var/www
# RUN  composer install && \
#     cp .env.example .env && \
#     php artisan key:generate && \
#     php artisan config:cache
EXPOSE 9000
ENTRYPOINT [ "php-fpm" ]
