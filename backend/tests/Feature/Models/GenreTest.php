<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class genreTest extends TestCase
{
    use DatabaseMigrations;
    public function testList()
    {
        //Cria registro usando factory
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $this->assertCount(1, $genres);
        $genreKey = array_keys($genres->first()->getAttributes());
        $expectGenreKey = [
            'id',
            'name',
            'created_at',
            'deleted_at',
            'updated_at',
            'is_active'
        ];
        $this->assertEqualsCanonicalizing($expectGenreKey, $genreKey);
        //$response = $this->get('/');

        // $response->assertStatus(200);
    }
    /** @test */
    public function testCreate()
    {

        /**
         *  Devolve somente registro
         *
         */

        $genre = genre::create(['name' => 'teste1']);
        $genre->refresh();

        $this->assertequals(36, strlen($genre->id));
        $this->assertequals('teste1', $genre->name);
        $this->asserttrue($genre->is_active);
        $genre = genre::create(['name' => 'teste1', 'is_active' => true]);
        $this->asserttrue($genre->is_active);

        $genre = Genre::create(['name' => 'teste1', 'is_active' => false]);
        $this->assertFalse($genre->is_active);
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create(
            [
                'is_active' => true
            ]
        )->first();
        $data = [
            'name' => 'test_name_updated',
            'is_active' => false
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }
    /** @test */
    public function testDelete()
    {

        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $genre->delete();
        $this->assertNull($genre->find($genre->id));
        $genre->restore();
        $this->assertNotNull($genre->find($genre->id));
    }
}
