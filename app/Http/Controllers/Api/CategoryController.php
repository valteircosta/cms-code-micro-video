<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryCollecion;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends BasicCrudController
{
    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean',
    ];

    /**
     * Using resource in controller, override method
     */
    public function index()
    {
        $collection = parent::index();
        //call for returning collection
        // return CategoryResource::collection($collection);
        return new CategoryCollecion($collection);
    }
    public function show($id)
    {
        $obj = parent::show($id);
        //Call CategoryResource
        return new CategoryResource($obj);
    }
    protected function model()
    {
        return Category::class;
    }
    protected function rulesStore()
    {
        return $this->rules;
    }
    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
