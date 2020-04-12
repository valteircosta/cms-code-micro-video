<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    public function testList()
    {
        //Cria registro usando factory
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $expectCategoryKey = [
            'id',
            'name',
            'description',
            'created_at',
            'deleted_at',
            'updated_at',
            'is_active'
        ];
        $this->assertEqualsCanonicalizing($expectCategoryKey, $categoryKey);
        //$response = $this->get('/');

        // $response->assertStatus(200);
    }
}
