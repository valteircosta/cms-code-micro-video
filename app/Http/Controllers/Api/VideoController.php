<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
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
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'genres_id' => [
                'required',
                'array',
                'exists:genres,id,deleted_at,NULL',
            ]
        ];
    }
    /** Override method para poder fazer os relacionamentos */
    public function store(Request $request)
    {
        /**
         * Modelo -1 mais manual
         *    try {
         *     \DB::beginTransaction();
         *     $obj = $this->model()::create($validatedData);
         *      Faz o relacionamente removendo o antigo relacionamento e incluindo o novo array
         *     $obj->categories()->sync($request->get('categories_id'));
         *     $obj->genres()->sync($request->get('genres_id'));
         *     \DB::commit();
         *     //code...
         * } catch (\Exception $execption) {
         *     \DB::rollBack();
         * }
         * */
        //Set rule belfore validation
        $this->addRuleIfGenresHasCategories($request);
        /** Faz validação */
        /** Faz filtro para somente usar campos fillAble */
        $validatedData =  $this->validate($request, $this->rulesStore());
        $self = $this;
        /** @var Video $obj */
        /** Esta closure faz a transação mais simples efetuando o rollback caso ocorra erro */
        $obj = \DB::transaction(function () use ($request, $validatedData, $self) {
            $obj = $this->model()::create($validatedData);
            $self->handleRelations($obj, $request);
            return $obj; // Escopo diferente
        });
        /** Refresh pega todos campos usados na operação */
        $obj->refresh();
        return $obj;
    }
    protected function addRuleIfGenresHasCategories(Request $request)
    {
        $categoriesId = $request->get('categories_id');
        $categoriesId = is_array($categoriesId) ? $categoriesId : [];
        $this->rules['genres_id'][] = new GenresHasCategoriesRule(
            $categoriesId
        );
    }
    protected function handleRelations($video, Request $request)
    {
        /** sync = Faz o relacionamente removendo o antigo relacionamento e incluindo o novo array  */
        $video->categories()->sync($request->get('categories_id'));
        $video->genres()->sync($request->get('genres_id'));
    }
    /** Override update par  */
    public function update(Request $request, $id)
    {

        $obj = $this->findOrFail($id);
        $this->addRuleIfGenresHasCategories($request);
        $validatedData =  $this->validate($request, $this->rulesStore());
        $self = $this;
        /** @var Video $obj */
        $obj = \DB::transaction(function () use ($request, $validatedData, $self, $obj) {
            $obj->update($validatedData);
            $self->handleRelations($obj, $request);
            return $obj; // Escopo diferente
        });
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
