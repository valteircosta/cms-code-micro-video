<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollecion extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->each(function ($categoy) {
                new CategoryResource($categoy);
            }),
            'key' => 'value',
            'key2' => 'value2',
            'key3' => 'value3',
        ];
    }
}
