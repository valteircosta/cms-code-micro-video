Anotações referente ao projeto/estudos

Parei Teste Crud Video 5:30 minutos

Chamando o arquivo que inicializa o projeto
sudo ./init_container.sh -> iniciar container
sudo ./init_container-build.sh -> construir container build com sudo

-- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * --
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
php artisan  make:model --help

Cria model, factory, migration, seeder e controller
php artisan make:model Models/Category --all
Faz na ordem abaixo:
- migration
- model
- controller
- factory
- seeder
- add DatabaseSeeder.php
- add route api.php



Roda seeder
php artisan migrate --seed
php artisan migrate:refresh --seed

Usando tinker para consultas
php artisan tinker
\App\Models\Category::all();
-- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * --
Test com PHP Unit
Test unit => Testes focados em itens e comportamento da classe, sem levar em consideração sua interação com objetos do mundo exterior a ela.
Test feature => Testes focados na interação da classe com outras classes.
Criar
php artisan make:test --help
php artisan make:test CategoryTest --unit  => Teste Unit
php artisan make:test CategoryTest Teste Feature
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
