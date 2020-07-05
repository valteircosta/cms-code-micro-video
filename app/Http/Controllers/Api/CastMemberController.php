<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Http\Request;

class CastMemberController extends BasicCrudController
{
    private $rules = [
 Parrei no 9 minuto
        "name" => "required|max:255",
        "is_active" => 'boolean'
    ];
    protected function model()
    {
        return Genre::class;
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
