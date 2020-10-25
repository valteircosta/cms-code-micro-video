#!/bin/bash
# Aqui devemos rodar o compose  pois aqui o que é realizado fica no volume compartilhado
# Go to folder backend
cd backend
# a imagem já está criada.
echo "Concede permissão na pasta....."
chown -R www-data:www-data .

echo "Instalando dependencias php....."
composer install

echo "Instalando dependencias node....."
npm install

echo "copiando .env da aplicacao..."
if [ ! -f ".env"]; then
cp .env.example .env
fi

echo "copiando .env.testing da aplicacao..."
if [ ! -f ".env.testing"]; then
cp .env.testing.example .env.testing
fi

dockerize -wait tcp://db:3306 -timeout 60s

# echo "Concede permissão na pasta....."
# find storage bootstrap/cache public -type f -exec chmod o+w {} \;
# find storage bootstrap/cache public -type d -exec chmod o+wx {} \;
# chmod o-w public/index.php
echo "Executando key:Generate....."
php artisan key:generate
echo "Executando migrate....."
php artisan migrate
php-fpm
