<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController
{

    private $rules;
    public function __construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id' => 'required|array|exists:genres,id',
        ];
    }
    /** Override method para poder fazer os relacionamentos */
    public function store(Request $request)
    {
        /** Faz validação */
        /** Faz filtro para somente usar campos fillAble */
        $validatedData =  $this->validate($request, $this->rulesStore());
        /** @var Video $obj */
        $obj = $this->model()::create($validatedData);
        /** Faz o relacionamente removendo o antigo relacionamento e incluindo o novo array  */
        $obj->categories()->sync($request->get('categories_id'));
        $obj->genres()->sync($request->get('genres_id'));
        /** Refresh pega todos campos usados na operação */
        $obj->refresh();
        return $obj;
    }
    /** Override update par  */
    public function update(Request $request, $id)
    {

        $obj = $this->findOrFail($id);
        $validatedData =  $this->validate($request, $this->rulesStore());
        $obj->update($validatedData);
        $obj->categories()->sync($request->get('categories_id'));
        $obj->genres()->sync($request->get('genres_id'));
        return $obj;
    }

    protected function model()
    {
        return Video::class;
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
