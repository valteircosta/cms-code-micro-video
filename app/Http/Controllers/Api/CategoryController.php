<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $rules = [
        "name" => "required|max:255",
        "is_active" => 'boolean'
    ];

    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
        /** Faz validação */
        $this->validate($request, $this->rules);
        /** Deve liberar inclusão em massa */
        $category =       Category::create($request->all());
        /**
         * Faz o refresh para pegar todos campos pois o eloquent traz apenas os que
         * foram utilizado na operação
         */
        $category->refresh();
        return $category;
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {

        $this->validate($request, $this->rules);
        $category->update($request->all());
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent(); //204
    }
}
