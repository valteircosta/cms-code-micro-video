Anotações referente ao projeto/estudos

Parei no video Reportando erro de syncronização Minuto 16:00
Chamando o arquivo que inicializa o projeto

-t * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * --
GPC - Google Plataform Cloud
1) Adicionar as imagens que não são disponibilizadas por padrão no container registry
Exemplo execução do trigger default-push-trigger-docker-compose.
Repositório valteircosta/cloud-build-docker-compose   onde tem um Dockerfile
As imagens ficam no Container registry do GCP

-- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * --
iniciar e fechar o projeto
docker-compose up -d
docker-compose down
docker-compose exec --user=1000 app bash

=====================
Ajuda
Deve agora entrar na pasta backend para executar o artisan
php artisan  make:model --help

Cria model, factory, migration, seeder e controller
php artisan make:model Models/Category --all

Cria o Observer
php artisan make:observer CategoryObserver --model=Models/Category

Para criar um relacionamento deve se respeitar a ordem alfabetica pois o laravel e rigoroso
Exemplo: veja que category está antes de video em ordem alfabetica no singular
1.1) Criar a migrate  php artisan make:migration create_category_video_table
1.2) Criar a migrate  php artisan make:migration create_genre_video_table
1.3) Criar o resource  -> php artisan make:resource CategoryResource
     Criar o resource collection -> php artisan make:resource CategoryCollecion --collection
    - O resource faz a serialização no laravel, anteriormente era usado o Fractal.
1.3) Criar filtro usando a biblioteca : tucker-eric/eloquentfilter => php artisan model:filter User
    - php artisan model:filter CategoryFilter
    cria na pasta padrão ModelFilters, caso queira pode passar o name space para criação
1.4 Criar um observer para centralizar a regra de negócios relativos a envio de dados para o RabbitMQ.
    - php artisan make:observer GenreObserver --model=Models\\Genre


Adicionando Model ao sistema em desenvolvimento
Faz na ordem abaixo:
- migration
- model
- controller
- factory
- seeder
- add DatabaseSeeder.php
- add route api.php
- add resource
- add filter
- add Observer utilizado para RabbitMQ


Roda seeder
php artisan migrate --seed
php artisan migrate:refresh --seed

Usando tinker para consultas
php artisan tinker
\App\Models\Category::all();

Usando tinker para criar entidades
php artisan tinker \App\Models\Category::all();
bash-5.0$ php artisan tinker -> vai para o shell
>>> factory(\App\Models\Category::class)->create()


-- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * --
Test com PHP Unit
Test unit => Testes focados em itens e comportamento da classe, sem levar em consideração sua interação com objetos do mundo exterior a ela.
Test feature => Testes focados na interação da classe com outras classes.
Criar
php artisan make:test --help
php artisan make:test CategoryTest --unit  => Teste Unit => cria testes unitários dentro da pasta Unit
php artisan make:test CategoryTest --unit  => \\Models\\CategoryUnitTest => teste com endereço da pasta
php artisan make:test CategoryTest  => Teste Feature => cria testes dentro da pasta Feature
Executanto testes
vendor/bin/phpunit
vendor/bin/phpunit --filter CategoryTest => Testa apenas CategoryTest
vendor/bin/phpunit --filter CategoryTest::testExample => Testa apenas o método testExample da class CategoryTest
vendor/bin/phpunit --filter testExample tests/Unit/CategoryTest => Testa apenas o método testExample da class CategoryTest
vendor/bin/phpunit tests/Unit/CategoryTest.php => Passa path da classe
vendor/bin/phpunit "tests\\Unit\\CategoryTest.php"  => Passa namespace

Anotações sobre teste
a) Sempre manter independência entre os ambiente (teste/develop/Produção), arquivos de variáveis, banco de dados , etc.
    - Criar dbs teste e produção com arquivo inidb.sql
    - Criar variáveis no docker-compose.yaml para path BD e arquivo .env.testing " -template ./.docker/app/.env.testing:.env.testing"
    - Comando "printenv" mostra as variaveis criadas no container
b) Methods uteis da classe TestCase
    - setUp => Executado antes de cada method de teste ideal para configurar variáveis
    - tearDown => Executado após cada teste ideal para limpar
    - setUpBeforeClass => Executado na criação da classe um unica vez
    - tearDownAfterClass => Executado na finalização da classe um unica vez
- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * --
