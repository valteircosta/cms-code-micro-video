<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class GenreController extends BasicCrudController
{
    private $rules = [
        "name" => "required|max:255",
        "is_active" => 'boolean',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
    ];

    /** Override method para poder fazer os relacionamentos */
    public function store(Request $request)
    {
        $validatedData =  $this->validate($request, $this->rulesStore());
        $self = $this;
        $obj = \DB::transaction(function () use ($request, $validatedData, $self) {
            $obj = $this->model()::create($validatedData);
            $self->handleRelations($obj, $request);
            return $obj; // Escopo diferente
        });
        $obj->refresh();
        //Because overridden method should end with get resource() method.
        $resource = $this->resource();
        return new $resource($obj);
    }

    protected function handleRelations($genre, Request $request)
    {
        $genre->categories()->sync($request->get('categories_id'));
    }

    public function update(Request $request, $id)
    {

        $obj = $this->findOrFail($id);
        $validatedData =  $this->validate($request, $this->rulesStore());
        $self = $this;
        $obj = \DB::transaction(function () use ($request, $validatedData, $self, $obj) {
            $obj->update($validatedData);
            $self->handleRelations($obj, $request);
        });
        //Because overridden method should end with get resource() method.
        $resource = $this->resource();
        return new $resource($obj);
    }

    protected function model()
    {
        return Genre::class;
    }
    protected function rulesStore()
    {
        return $this->rules;
    }
    protected function rulesUpdate()
    {
        return $this->rules;
    }
    //Puts above the resource() method because it is using her
    protected function resourceCollection()
    {
        return $this->resource();
    }
    protected function resource()
    {
        return GenreResource::class;
    }
}
