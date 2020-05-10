<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{
    protected abstract function model();
    protected abstract function rulesStore();

    public function index()
    {
        return $this->model()::all();
    }
    public function store(Request $request)
    {
        /** Faz validação */
        /** Faz filtro para somente usar campos fillAble */
        $validatedData =  $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($validatedData);
        /** Refresh pega todos campos usados na operação */
        $obj->refresh();
        return $obj;
    }

    // public function store(Request $request)
    // {
    //     /** Faz validação */
    //     $this->validate($request, $this->rules);
    //     /** Deve liberar inclusão em massa */
    //     $category =       Category::create($request->all());
    //     /**
    //      * Faz o refresh para pegar todos campos pois o eloquent traz apenas os que
    //      * foram utilizado na operação
    //      */
    //     $category->refresh();
    //     return $category;
    // }

    // public function show(Category $category)
    // {
    //     return $category;
    // }

    // public function update(Request $request, Category $category)
    // {

    //     $this->validate($request, $this->rules);
    //     $category->update($request->all());
    //     return $category;
    // }

    // public function destroy(Category $category)
    // {
    //     $category->delete();
    //     return response()->noContent(); //204
    // }
}
