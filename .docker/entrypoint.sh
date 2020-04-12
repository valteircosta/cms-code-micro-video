#!/bin/bash
# Aqui devemos rodar o compose  pois aqui o que é realizado fica no volume compartilhado
# a imagem já está criada.
echo "Instalando dependencias php....."
composer install
echo "Instalando dependencias node....."
npm install

echo "Copiando .env da aplicacao..."
dockerize -template ./.docker/app/.env:.env
dockerize -template ./.docker/app/.env.testing:.env.testing

echo "Aguardando conexao com o banco de dados..."
dockerize -wait tcp://db:3306 -timeout 60

echo "Concede permissão na pasta....."
find storage bootstrap/cache public -type f -exec chmod o+w {} \;
find storage bootstrap/cache public -type d -exec chmod o+wx {} \;
chmod o-w public/index.php
echo "Executando key:Generate....."
php artisan key:generate
echo "Executando migrate....."
php artisan migrate
php-fpm
