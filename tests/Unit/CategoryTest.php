<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\TestCase;
use Route;

/**
 * Teste de itens e propriedades da unit
 * Não testa nada que vem de fora da classe
 * Esta mudanças serão testadas em feature
 */

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
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
        $Casts = ['id' => 'string'];
        $this->assertEquals(
            $Casts,
            $this->category->getCasts()
        );
    }
    public function testFillable()
    {
        Category::create(['name' => 'teste']);
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals(
            $fillable,
            $this->category->getfillable()
        );
    }
}
