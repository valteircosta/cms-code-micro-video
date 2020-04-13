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
    /** @test */
    public function testCreate()
    {
        $category = Category::create(['name' => 'teste1']);
        $category->refresh();
        $this->assertEquals('teste1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create(['name' => 'teste1', 'description' => null]);
        $this->assertNull($category->description);

        $category = Category::create(['name' => 'teste1', 'description' => 'description1']);
        $this->assertEquals('description1', $category->description);

        $category = Category::create(['name' => 'teste1', 'is_active' => true]);
        $this->assertTrue($category->is_active);

        $category = Category::create(['name' => 'teste1', 'is_active' => false]);
        $this->assertFalse($category->is_active);
    }
}
