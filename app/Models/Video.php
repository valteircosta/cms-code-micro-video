<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes, Traits\Uuid, Traits\UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    const THUMB_FILE_MAX_SIZE = 1024 * 5; // 5 MB
    const BANNER_FILE_MAX_SIZE = 1024 * 10; // 10 MB
    const TRAILER_FILE_MAX_SIZE = 1024 * 1025 * 1; // 1 GB
    const VIDEO_FILE_MAX_SIZE = 1024 * 1025 * 50; // 50 GB


    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'thumb_file',
        'video_file',
        'banner_file',
        'trailer_file',
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
    protected $hidden = ['video_file', 'thumb_file', 'banner_file', 'trailer_file'];
    public static $fileFields = ['video_file', 'thumb_file', 'banner_file', 'trailer_file'];

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
                $obj->deleteFiles($files);
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

        $files = self::extractFiles($attributes);
        try {
            //Disable auto commit
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($saved) {
                //Do upload new file here
                $this->uploadFiles($files);
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

    /**
     * Methods below work with concepts mutations of the Laravel
     * getThumbFileAttribute()  producer  $video->thumb_file_url
     */
    public function getThumbFileUrlAttributre()
    {
        return $this->thumb_file ? $this->getFileUrl($this->thumb_file) : null;
    }
    public function getBannerFileUrlAttributre()
    {
        return $this->banner_file ? $this->getFileUrl($this->banner_file) : null;
    }
    public function getTrailerFileUrlAttributre()
    {
        return $this->trailer_file ? $this->getFileUrl($this->trailer_file) : null;
    }
    public function getVideoFileUrlAttributre()
    {
        return $this->video_file ? $this->getFileUrl($this->video_file) : null;
    }
}
