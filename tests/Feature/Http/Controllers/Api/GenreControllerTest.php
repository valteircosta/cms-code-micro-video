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
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }
    public function testShow()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }
    /** @test */
    public function testInvalidationData()
    {

        $data = [
            'name' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
        $data = [
            'name' => str_repeat('a', 266),
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
        $data = [
            'is_active' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
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
        $data =    [
            'name' => 'test'
        ];

        $testDatabase = $data + [
            'is_active' => true,
            'deleted_at' => null,
        ];

        $testJsonData = $data + [
            'is_active' => true,
            'deleted_at' => null,
        ];
        $response = $this->assertStore($data, $testDatabase, $testJsonData);
        $response->assertJsonStructure(['deleted_at', 'created_at']);
        $data =    [
            'name' => 'test',
            'is_active' => false,
        ];
        $testDatabase = $data;
        $this->assertStore($data, $testDatabase);
    }

    public function testUpdate()
    {
        $data =    [
            'name' => 'name',
        ];
        $this->category = factory(Genre::class)->create(
            $data
        );

        $data['name'] = 'test';
        $data['is_active'] = false;

        $testDatabase = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $testJsonData = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $response = $this->assertUpdate($data, $testDatabase, $testJsonData);
        $response->assertJsonStructure(
            ['deleted_at', 'created_at']
        );
        $data['is_active'] = false;
        $testDatabase = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $testJsonData = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);
        $response = $this->assertUpdate($data, $testDatabase, $testJsonData);
        $response->assertJsonFragment(
            $testJsonData
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

    protected  function routeStore()
    {
        return route('genres.store');
    }
    protected  function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }
    protected function model()
    {
        return Genre::class;
    }
}
