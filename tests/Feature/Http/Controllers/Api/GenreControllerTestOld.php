<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\This;
use Route;
use Tests\TestCase;

class GenreControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations;
    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }
    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }
    /** @test */
    public function testInvalidationData()
    {

        //Make request POST
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertInvalidationRequired($response);
        $response = $this->json(
            'POST',
            route('genres.store'),
            [
                'name' => str_repeat('a', 266),
                'is_active' => 'a'
            ]
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        //Make request for put
        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id], []));
        $this->assertInvalidationRequired($response);

        //Params for PUT
        $response = $this->json(
            'PUT',
            route(
                'genres.update',
                ['genre' => $genre->id]
            ),
            [
                'name' => str_repeat('a', 266),
                'is_active' => 'a'
            ]

        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }
    //Not use prefix test in helper methods
    private function assertInvalidationRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']) // Field is_active não está presente
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }
    private function assertInvalidationMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    private function assertInvalidationBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                //O char undescore _ volta # is_active => is active
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }
    /** @testStore */
    public function testStore()
    {

        $response = $this->json(
            'POST',
            route('genres.store'),
            [
                'name' => 'test'
            ]
        );
        $id = $response->json('id');
        $genre = Genre::find($id);
        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json(
            'POST',
            route('genres.store'),
            [
                'name' => 'test',
                'is_active' => false
            ]
        );

        $response
            ->assertJsonFragment(
                [
                    'is_active' => false
                ]
            );
    }

    public function testUpdate()
    {

        $genre = factory(Genre::class)->create(
            [
                'is_active' => false
            ]
        );

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            [
                'name' => 'test',
                'is_active' => true,
            ]
        );

        $id = $response->json('id');
        $genre = Genre::find($id);
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
        $response->assertJsonFragment(
            [
                'is_active' => true,
            ]
        );
    }
    /** @test */
    public function testDestroy()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->json(
            'DELETE',
            route('genres.destroy', ['genre' => $genre->id])
        );
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }
}
