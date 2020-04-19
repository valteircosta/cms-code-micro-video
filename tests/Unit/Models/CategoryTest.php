<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Route;

/**
 * Teste de itens e propriedades da unit
 * Não testa nada que vem de fora da classe
 * Esta mudanças serão testadas em feature
 */

class CategoryTest extends TestCase
{
    private $category;

    //Executado somente na criação da classe de teste
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    protected function tearDown(): void
    {
        // Executar operações antes dele acontecer
        parent::tearDown();
        // Executar operações após ele, já pode haver ocorrido erros
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }



    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIncrementing()
    {
        $this->assertEquals(false, $this->category->incrementing);
    }
    public function testDates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        /**Testa item por item */
        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        // Teste de quantidade de itens
        $this->assertCount(count($dates), $this->category->getDates());
    }
    public function testIfUseTraits()
    {


        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        //print_r(class_uses(Category::class));
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }
    public function testCasts()
    {
        $Casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals(
            $Casts,
            $this->category->getCasts()
        );
    }
    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals(
            $fillable,
            $this->category->getfillable()
        );
    }
}
