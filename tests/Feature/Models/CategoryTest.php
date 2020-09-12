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

        /**
         *  Devolve somente registro
         *
         */

        $category = Category::create(['name' => 'teste1']);
        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
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

    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create(
            [
                'description' => 'test_descripition',
                'is_active' => true
            ]
        )->first();
        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_update',
            'is_active' => false
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }
    /** @test */
    public function testDelete()
    {

        /** @var Category $category */
        $category = factory(Category::class)->create();
        $category->delete();
        $this->assertNull($category->find($category->id));
        $category->restore();
        $this->assertNotNull($category->find($category->id));
    }
}
