Anotações referente ao projeto/estudos

iniciar e fechar o projeto
docker-compose up -d
docker-compose down
=======================
Ajuda
php artisan  make:model --help

Cria model, factory, migration, seeder e controller
php artisan make:model Models/Category --all

Roda seeder
php artisan migrate --seed
php artisan migrate:refresh --seed

Usando tinker para consultas
php artisan tinker
\App\Models\Category::all();
