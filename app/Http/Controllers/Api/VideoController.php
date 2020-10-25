<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VideoResource;
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
            ],
            'thumb_file' => 'image|max:' . Video::THUMB_FILE_MAX_SIZE, // 5 MB
            'banner_file' => 'image|max:' . Video::BANNER_FILE_MAX_SIZE, // 10 MB
            'trailer_file' => 'mimetypes:video/mp4|max:' . Video::TRAILER_FILE_MAX_SIZE, // 1 GB
            'video_file' => 'mimetypes:video/mp4|max:' . Video::VIDEO_FILE_MAX_SIZE // 50 GB, //KB
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
        $obj = $this->model()::create($validatedData);
        $obj->refresh();
        //Because overridden method should end with get resource() method.
        $resource = $this->resource();
        return new $resource($obj);
    }
    protected function addRuleIfGenresHasCategories(Request $request)
    {
        $categoriesId = $request->get('categories_id');
        $categoriesId = is_array($categoriesId) ? $categoriesId : [];
        $this->rules['genres_id'][] = new GenresHasCategoriesRule(
            $categoriesId
        );
    }
    /** Override update par  */
    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $this->addRuleIfGenresHasCategories($request);
        $validatedData =  $this->validate($request, $this->rulesStore());
        $obj->update($validatedData);
        //Because overridden method should end with get resource() method.
        $resource = $this->resource();
        return new $resource($obj);
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
    //Puts above the resource() method because it is using her
    protected function resourceCollection()
    {
        return $this->resource();
    }
    protected function resource()
    {
        return VideoResource::class;
    }
}
