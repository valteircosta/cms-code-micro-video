<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Route;

/**
 * Teste de itens e propriedades da unit
 * Não testa nada que vem de fora da classe
 * Esta mudanças serão testadas em feature
 */

class VideoUnitTest extends TestCase
{
    private $video;


    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIncrementing()
    {
        $this->assertFalse($this->video->incrementing);
    }

    public function testDates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        /**Testa item por item */
        foreach ($dates as $date) {
            $this->assertContains($date, $this->video->getDates());
        }
        // Teste de quantidade de itens
        $this->assertCount(count($dates), $this->video->getDates());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        //print_r(class_uses(Video::class));
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);
    }
    public function testCasts()
    {

        $Casts = [
            'id' => 'string',
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer'
        ];

        $this->assertEquals(
            $Casts,
            $this->video->getCasts()
        );
    }
    public function testFillable()
    {
        $fillable = [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration'
        ];
        $this->assertEquals(
            $fillable,
            $this->video->getfillable()
        );
    }
}
