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
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIncrementing()
    {
        $category = new Category();
        $this->assertEquals(false, $category->incrementing);
    }
    public function testDates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $category = new Category();
        /**Testa item por item */
        foreach ($dates as $date) {
            $this->assertContains($date, $category->getDates());
        }
        // Teste de quantidade de itens
        $this->assertCount(count($dates), $category->getDates());
    }
    public function testIfUseTraits()
    {
        Genre::create(['name' => 'teste creating']);
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
        $category = new Category();
        $this->assertEquals(
            $Casts,
            $category->getCasts()
        );
    }
    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $category = new Category();
        $this->assertEquals(
            $fillable,
            $category->getfillable()
        );
    }
}
