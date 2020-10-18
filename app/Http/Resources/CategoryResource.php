<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    //Disable wrap Data
    //public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //Define field that returned
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        // ];
        // Keep simple stupid, not exist field for hide
        return parent::toArray($request);
    }
}
