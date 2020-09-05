<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Console\Presets\Bootstrap;

class Category extends Model
{
    use SoftDeletes, Traits\Uuid;

    protected $fillable = ['name', 'description', 'is_active'];
    protected $dates = ['deleted_at'];
    //Propriedade que faz o cast dos campos informado
    protected $casts = ['id' => 'string', 'is_active' => 'boolean'];
    //Evita incremento do id
    public $incrementing = false;
}
