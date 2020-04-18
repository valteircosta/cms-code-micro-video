<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Route;
use Tests\TestCase;

class CategoryControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations;
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

        $response = $this->json('POST', route('categories.store'), []);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']) // Field is_active não está presente
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);

        //Make request
        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 266),
            'is_active' => 'a'
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ])
            ->assertJsonFragment([
                //O char undescore _ volta # is_active => is active
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);

        //Make request for put
        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id], []));
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']) // Field is_active não está presente
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);

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

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is active']) // Field is_active não está presente
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ])
            ->assertJsonFragment([
                //O char undescore _ volta # is_active => is active
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }
}
