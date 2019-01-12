<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClipCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     * https://medium.com/@jeffochoa/consuming-third-pary-apis-with-laravel-resources-c13a0c7dc945
     */
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }

    public function toArray($request)
    {
        return [
            'data' => $this->collection
                          ->map
                          ->toArray($request)
                          ->all(),
            'links' => [
               'self' => 'link-value',
             ],
        ];
    }
}
