<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BasicCrudController extends Controller
{
    protected $paginationSize = 15;
    protected abstract function model();
    protected abstract function rulesStore();
    protected abstract function rulesUpdate();
    protected abstract function resource();
    protected abstract function resourceCollection();

    public function index()
    {

        // if paginationSize = 0 or null then return all registers else return model paginationSize,
        $data = !$this->paginationSize ? $this->model()::all() : $this->model()::paginate($this->paginationSize);
        $resouceCollectionClass = $this->resourceCollection();
        // Using reflection get instance this class
        $refClass = new \ReflectionClass($resouceCollectionClass);
        return $refClass->isSubclassOf(ResourceCollection::class)
            ? $resouceCollectionClass($data)
            : $resouceCollectionClass::collection($data);
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
        return new $resource($obj);
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
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function update(Request $request, $id)
    {

        $obj = $this->findOrFail($id);
        $validatedData =  $this->validate($request, $this->rulesStore());
        $obj->update($validatedData);
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function destroy($id)
    {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent(); //204
    }
}
