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
        \dd($response->context());
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

        $data =    [
            'name' => 'name',
            'description' => 'description',
            'is_active' => false
        ];
        // $this->category = factory(Category::class)->create(
        //     $data
        // );

        $testDatabase = $data + [
            'name' => 'test',
            'description' => 'test',
            'is_active' => true,
            'deleted_at' => null,
        ];

        $testJsonData = $data + [
            'description' => 'test',
            'is_active' => true,
            'deleted_at' => null,
        ];

        $response = $this->assertUpdate($data, $testDatabase, $testJsonData);
        $response->assertJsonStructure(
            ['deleted_at', 'created_at']
        );

        $testDatabase = array_merge($data + [
            'description' => '',
            'is_active' => true,
            'deleted_at' => null,
        ]);

        $testJsonData = array_merge($data + [
            'description' => '',
            'is_active' => true,
            'deleted_at' => null,
        ]);

        $response = $this->assertUpdate($data, $testDatabase, $testJsonData);
        $response->assertJsonFragment(
            $testJsonData
        );
        $data['description'] = 'updateNotNull';
        $testDatabase =  [
            'description' => 'updateNotNull',
        ];

        $testJsonData = [
            'description' => 'updateNotNull',
        ];
        $response = $this->assertUpdate($data, $testDatabase);
        $response->assertJsonFragment(
            $testJsonData
        );

        $data['description'] = null;
        $testDatabase =  [
            'description' => null
        ];

        $testJsonData = [
            'description' => null
        ];
        $response = $this->assertUpdate($data, $testDatabase);
        $response->assertJsonFragment(
            $testJsonData
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
