<?php

namespace App\Models;

use App\ModelFilters\GenreFilter;
use App\Models\Traits\SerializeDateToIso8601;
use App\Models\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes, Uuid, Filterable, SerializeDateToIso8601;

    protected $fillable = ['name', 'is_active'];
    protected $dates = ['deleted_at'];
    //Propriedade que faz o cast dos campos informado
    protected $casts = ['id' => 'string', 'is_active' => 'boolean'];
    public $incrementing = false;
    protected $keyType = 'string';

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
