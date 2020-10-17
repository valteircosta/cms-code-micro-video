<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

abstract class BasicCrudController extends Controller
{
    protected abstract function model();
    protected abstract function rulesStore();
    protected abstract function rulesUpdate();
    protected abstract function resource();

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
        //Implementing resource
        $resource = $this->resource();
        return new Resource($obj);
    }
    protected function findOrFail($id)
    {
        $model = $this->model();
        /** Obtem a coluna usada no where */
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id)
    {
        $obj = $this->findOrFail($id);
        return $obj;
    }

    public function update(Request $request, $id)
    {

        $obj = $this->findOrFail($id);
        $validatedData =  $this->validate($request, $this->rulesStore());
        $obj->update($validatedData);
        return $obj;
    }

    public function destroy($id)
    {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent(); //204
    }
}
