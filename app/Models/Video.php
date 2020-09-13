<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes, Traits\Uuid;

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

    //First is called magic method what call create, it not exist then called
    // QueryBuilder constructor
    public static  function create(array $attributes = [])
    {
        try {
            //Disable auto commit
            \DB::beginTransaction();
            $obj = static::query()->create($attributes);
            // Do upload here
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
    public function categories()
    {
        //with trashed traz a categorias antigas que já foram excluidas
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }
}
