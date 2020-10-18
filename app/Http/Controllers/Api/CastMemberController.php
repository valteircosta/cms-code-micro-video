<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Http\Request;

class CastMemberController extends BasicCrudController
{
    private $rules;
    public function __construct()
    {
        $this->rules = [
            "name" => "required|max:255",
            "type" => "required|in:" . implode(',', [CastMember::TYPE_ACTOR, CastMember::TYPE_DIRECTOR])
        ];
    }
    protected function model()
    {
        return CastMember::class;
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
        return CastMemberResource::class;
    }
}
