<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $genre;

    private $serializedFields = [
        'id',
        'name',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ]
    ];

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
            ->assertJson(['meta' => ['per_page' => 15]])
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => $this->serializedFields
                    ],
                    'links' => [],
                    'meta' => [],

                ]
            );
        $resource = GenreResource::collection(collect([$this->genre]));
        $this->assertResource($response, $resource);
    }
    public function testShow()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->serializedFields])
            ->assertJsonFragment($this->genre->toArray());
        $id = $response->json('data.id');
        $resource = new GenreResource(Genre::find($id));
        $this->assertResource($response, $resource);
    }
    /** @test */
    public function testInvalidationData()
    {

        $data = [
            'name' => '',
            'categories_id' => '',
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

        $data = [
            'categories_id' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
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
    /** @testSaves */
    public function testSaves()
    {
        $categoryId = factory(Category::class)->create()->id;
        $data = [
            [
                'send_data' => [
                    'name' => 'test',
                    'categories_id' => [$categoryId]
                ],
                'test_data'  => [
                    'name' => 'test',
                    'is_active' => true,
                ],
                'send_data' => [
                    'name' => 'test',
                    'is_active' => false,
                    'categories_id' => [$categoryId]
                ],
                'test_data'  => [
                    'name' => 'test',
                    'is_active' => false,
                ],
            ]
        ];
        foreach ($data as $test) {
            //Store test
            $response = $this->assertStore($test['send_data'], $test['test_data']);
            $response->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
            $this->assertResource(
                $response,
                new GenreResource(Genre::find($response->json('data.id')))
            );
            //Update test
            $response = $this->assertUpdate($test['send_data'], $test['test_data']);
            $response->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
            $this->assertHasCategory($response->json('data.id'), $categoryId);
            $this->assertResource(
                $response,
                new GenreResource(Genre::find($response->json('data.id')))
            );
        }
    }
    protected  function assertHasCategory($genreId, $categoryiId)
    {
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $genreId,
            'category_id' => $categoryiId
        ]);
    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'test',
            ]);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(Request::class);
        $hasErro = false;
        try {
            $controller->store($request);
        } catch (TestException $exception) {
            $this->assertCount(1, Genre::all());
            $hasErro = true;
        }
        $this->assertTrue($hasErro);
    }

    public function testRollbackUpdate()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);


        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'test',
            ]);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(Request::class);
        $hasErro = false;
        try {
            $controller->update($request, 1);
        } catch (TestException $exception) {
            $this->assertCount(1, Genre::all());
            $hasErro = true;
        }
        $this->assertTrue($hasErro);
    }


    /** @test */
    public function testDestroy()
    {
        $response = $this->json(
            'DELETE',
            route('genres.destroy', ['genre' => $this->genre->id])
        );
        $response->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }
    public function testSyncCategories()
    {
        //Cria com helper, gera array de ids com pluck => tipo  ['1','2','2']
        $categoriesId = Factory(Category::class, 3)->create()->pluck('id')->toArray();
        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];
        //Inclui com a category index 0
        $response = $this->json('POST', $this->routeStore(), $sendData);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('data.id')
        ]);

        //array com novos relations
        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];
        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $response->json('data.id')]),
            $sendData
        );
        //Valida não existencia
        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('data.id')
        ]);
        //Valida a existência
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[1],
            'genre_id' => $response->json('data.id')
        ]);

        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[2],
            'genre_id' => $response->json('data.id')
        ]);
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
