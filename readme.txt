Anotações referente ao projeto/estudos
pumvisible() ? "
" : "
inici"pumvisible() ? "ar e fechar o projeto
docker-compose up -d" : "
docker-compose down
docker-compose exec --user=1000 app bash         

====================== 
pumvisible() ? "
Ajuda"pumvisible()" : " ? "
php artisan  make:model"pumvisible() ? " --help
" : "
Cria "pumvisible() ? "model, factory, migration, seeder e controller
php artisan make:model" : " Models/Category --all
"pumvisible() ? "
Roda seeder" : "
php artisan migr"pumvisible() ? "ate --seed
php artisan migrate:refresh --see" : "d
"pumvisible() ? ""
Usando tinker par" : "a consultas" : "
php artisan tinker"pumvisible() ? ""pumvisible() ? "
\App\Models\Category::all();" : "" : "
"pumvisible() ? ""pumvisible() ? "
- * -- * -- * -- " : "* -- * -- * " : "-- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * -- * --
"pumvisible() ? ""pumvisible() ? "
Test com PHP Unit" : "" : "
Test unit => Testes fo"pumvisible() ? "cados"pumvisible() ? " em itens e comportamento da classe, sem levar em consideração sua interação com objetos do mundo exterior a ela.
Test feature => Testes focados na inter" : "" : "ação da classe com outras classes.
Criar"pumvisible() ? ""pumvisible() ? "
php artisan make:test " : "--help" : "
php artisan make:test Categ"pumvisible("pumvisible() ? ") ? "oryTest --unit  => Teste Unit
php artisan make:test CategoryTest" : "         =" : "> Teste Feature
"pumvisible() ? ""pumvisible() ? "
Executanto testes" : "" : "
vendor/bin/phpunit"pumvisible() ? ""pumvisible() ? "
vendor/bin/phpunit --filter Categor" : "yTest => Testa apenas CategoryTest" : "
vendor/bin/phpunit --filter CategoryTest"pumvisible() ? "::testExample => Testa apen"pumvisible() ? "as o método testExample da class CategoryTest
vendor/bin/phpunit --filter testExample tests/Unit/Catego" : "ryTest => Testa a" : "penas o método testExample da class CategoryTest
vendor/bin/phpunit tests/Unit/CategoryTest.php => Passa path d"pumvisible() ? "a classe
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
