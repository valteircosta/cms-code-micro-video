<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Route;

/**
 * Teste de itens e propriedades da unit
 * Não testa nada que vem de fora da classe
 * Esta mudanças serão testadas em feature
 */

class genreTest extends TestCase
{
    private $genre;


    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIncrementing()
    {
        $this->assertEquals(false, $this->genre->incrementing);
    }
    public function testDates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        /**Testa item por item */
        foreach ($dates as $date) {
            $this->assertContains($date, $this->genre->getDates());
        }
        // Teste de quantidade de itens
        $this->assertCount(count($dates), $this->genre->getDates());
    }
    public function testIfUseTraits()
    {


        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        //print_r(class_uses(Genre::class));
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }
    public function testCasts()
    {
        $Casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals(
            $Casts,
            $this->genre->getCasts()
        );
    }
    public function testFillable()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals(
            $fillable,
            $this->genre->getfillable()
        );
    }
}
