<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BasicCrudController extends Controller
{
    protected $defaultPerPage = 15;
    protected abstract function model();
    protected abstract function rulesStore();
    protected abstract function rulesUpdate();
    protected abstract function resource();
    protected abstract function resourceCollection();

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', $this->defaultPerPage);
        $hasFilter = \in_array(Filterable::class, \class_uses($this->model()));

        $query = $this->queryBuilder();
        if ($hasFilter) {
            $query = $query->filter($request->all());
        }
        // if defaultPerPage = 0 or null then return all registers else return model defaultPerPage,
        $data = $request->has('all') || !$this->defaultPerPage
            ? $query->get()
            : $query->paginate($perPage);

        //$data = !$this->defaultPerPage ? $this->model()::all() : $this->model()::paginate($this->defaultPerPage);
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
        $obj = $this->queryBuilder()::create($validatedData);
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
        return $this->queryBuilder()::where($keyName, $id)->firstOrFail();
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

    protected function queryBuilder(): Builder
    {
        return $this->model()::query();
    }
}
