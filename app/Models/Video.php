<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;

class Video extends Model
{
    use SoftDeletes, Traits\Uuid, Traits\UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
    ];

    protected $dates = ['deleted_at'];

    //Propriedade que faz o cast dos campos informado
    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public $incrementing = false;
    public static $fileFields = ['video_file'];

    //First  magic method is called wich call create, if create does not exist then next line
    // QueryBuilder constructor is called
    public static  function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try {
            //Disable auto commit
            \DB::beginTransaction();
            /** @var Video $obj */
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            // Do upload here
            $obj->uploadFiles($files);
            \DB::commit();
            return $obj;
        } catch (\Exception $e) {
            if (isset($obj)) {
                // Do delete uploaded files
            }
            \DB::rollBack();
            throw $e; // Rise execption for laravel
        }
    }
    /**
     * Override method update Model
     * @param array $attributes
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function update(array $attributes = [], array $options = [])
    {
        try {
            //Disable auto commit
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($saved) {
                //Do upload new file here
                //Do delete older file
            }
            \DB::commit();
            return $saved;
        } catch (\Exception $e) {
            //Do delete new files try update here
            \DB::rollBack();
            throw $e; // Rise execption for laravel
        }
    }

    public static function handleRelations(Video $video, array  $attributes)
    {
        /** sync = Faz o relacionamente removendo o antigo relacionamento e incluindo o novo array  */
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }
        if (isset($attributes['genres_id'])) {
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public function categories()
    {
        //with trashed traz a categorias antigas que já foram excluidas
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    protected function uploadDir()
    {
        return $this->id;
    }
}
