<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\This;
use Route;
use Tests\TestCase;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations, TestValidations;
    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }
    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));
        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }
    /** @test */
    public function testInvalidationData()
    {
        $data = [
            'name' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $data = [
            'name' => str_repeat('a', 266),
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $data = [
            'is_active' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');

        //Make request for put
        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id], []));
        $this->assertInvalidationRequired($response);

        //Params for PUT
        $response = $this->json(
            'PUT',
            route(
                'categories.update',
                ['category' => $category->id]
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
        // Chama a traits de validação
        $this->assertValidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['is_active']); // Field is_active não está presente
    }
    private function assertInvalidationMax(TestResponse $response)
    {
        $this->assertValidationFields($response, ['name'], 'max.string', ['max' => 255]);
    }

    private function assertInvalidationBoolean(TestResponse $response)
    {
        $this->assertValidationFields($response, ['is_active'], 'boolean');
    }
    /** @testStore */
    public function testStore()
    {

        $response = $this->json(
            'POST',
            route('categories.store'),
            [
                'name' => 'test'
            ]
        );
        $id = $response->json('id');
        $category = Category::find($id);
        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json(
            'POST',
            route('categories.store'),
            [
                'name' => 'test',
                'description' => 'description',
                'is_active' => false
            ]
        );

        $response
            ->assertJsonFragment(
                [
                    'description' => 'description',
                    'is_active' => false
                ]
            );
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
        $category = factory(Category::class)->create();
        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => $category->id])
        );
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }
    protected  function routeStore()
    {
        return route('categories.store');
    }
}
