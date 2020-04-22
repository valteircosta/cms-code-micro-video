<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }


    public function testIndex()
    {
        $response = $this->get(route('categories.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);
    }
    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());
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

    public function testStore()
    {
        $data =    [
            'name' => 'test'
        ];

        $testDatabase = $data + [
            'description' => null,
            'is_active' => true,
            'deleted_at' => null,
        ];

        $testJsonData = $data + [
            'description' => null,
            'is_active' => true,
            'deleted_at' => null,
        ];
        $response = $this->assertStore($data, $testDatabase, $testJsonData);
        $response->assertJsonStructure(['deleted_at', 'created_at']);
        $data =    [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false,
        ];
        $testDatabase = $data;
        $this->assertStore($data, $testDatabase);
    }

    public function testUpdate()
    {

        $category = factory(Category::class)->create(
            [
                'description' => 'description',
                'is_active' => false
            ]
        );

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => 'test',
                'is_active' => true,
            ]
        );

        $id = $response->json('id');
        $category = Category::find($id);
        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
        $response->assertJsonFragment(
            [
                'description' => 'test',
                'is_active' => true,
            ]
        );

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => '',
            ]
        );
        $response->assertJsonFragment(

            [
                'description' => null,
            ]
        );

        // test null
        $category->description = 'test';
        $category->save();

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => null,
            ]
        );
        $response->assertJsonFragment(

            [
                'description' => null,
            ]
        );
    }
    /** @test */
    public function testDestroy()
    {
        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => $this->category->id])
        );
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }
    protected  function routeStore()
    {
        return route('categories.store');
    }
    protected  function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }
    protected function model()
    {
        return Category::class;
    }
}
