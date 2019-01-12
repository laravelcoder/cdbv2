<?php

namespace App\Http\Resources;
use App\Clip;
Use App\User;
use Spatie\MediaLibrary\Models\Media;

use Illuminate\Http\Resources\Json\JsonResource;

class ClipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        'videos' => $this->getMedia('videos'),
//            'images' =>  $this->getMedia('images')
//        $videos = $this->getMedia('videos');
//        $images = $this->getMedia('images');
        $clip = $this->clip;
        $videos = $this->getMedia('videos');
//      return parent::toArray($request);
        return [
            'id' => $this->id,
            'creator'=> $this->creator,
            'videos' => $this->getMedia('videos')
//            'images' =>  $this->getMedia('images')
        ];
    }


}
