<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Route;

/**
 * Teste de itens e propriedades da unit
 * Não testa nada que vem de fora da classe
 * Esta mudanças serão testadas em feature
 */

class CastMemberUnitTest extends TestCase
{
    private $cast_member;


    protected function setUp(): void
    {
        parent::setUp();
        $this->cast_member = new CastMember();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIncrementing()
    {
        $this->assertFalse($this->cast_member->incrementing);
    }

    public function testDates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        /**Testa item por item */
        foreach ($dates as $date) {
            $this->assertContains($date, $this->cast_member->getDates());
        }
        // Teste de quantidade de itens
        $this->assertCount(count($dates), $this->cast_member->getDates());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        //print_r(class_uses(CastMember::class));
        $cast_memberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits, $cast_memberTraits);
    }
    public function testCasts()
    {

        $Casts = ['id' => 'string', 'name' => 'string', 'type' => 'integer'];
        $this->assertEquals(
            $Casts,
            $this->cast_member->getCasts()
        );
    }
    public function testFillable()
    {
        $fillable = ['name', 'type'];
        $this->assertEquals(
            $fillable,
            $this->cast_member->getfillable()
        );
    }
}
