<?php

namespace App\Models;

use App\ModelFilters\CategoryFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, Traits\Uuid, Filterable;

    protected $fillable = ['name', 'description', 'is_active'];
    protected $dates = ['deleted_at'];
    //Propriedade que faz o cast dos campos informado
    protected $casts = ['id' => 'string', 'is_active' => 'boolean'];
    //Evita incremento do id
    public $incrementing = false;

    //Do filter in class using CategoryFilter
    public function modelFilter()
    {
        return $this->provideFilter(CategoryFilter::class);
    }
}
