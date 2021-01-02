<?php

namespace App\Models;

use App\ModelFilters\GenreFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes, Traits\Uuid, Filterable;

    protected $fillable = ['name', 'is_active'];
    protected $dates = ['deleted_at'];
    //Propriedade que faz o cast dos campos informado
    protected $casts = ['id' => 'string', 'is_active' => 'boolean'];
    public $incrementing = false;

    public function categories()
    {
        //Traz os que foram excluídos logicamente
        return $this->belongsToMany(Category::class)->withTrashed();
    }
     //Do filter in class using Filter
    public function modelFilter()
    {
        return $this->provideFilter(GenreFilter::class);
    }
}
