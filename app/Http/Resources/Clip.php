<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Clip extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        return [
                'videos' => $this->getMedia('videos'),
                'images' => $this->getMedia('images'),
                'percentage' => $this->percentage,
                'creator' => $this->creator,
                    'convert_video_for_downloading' => $this->convert_video_for_downloading,
                    'convert_video_for_streaming' => $this->convert_video_for_streaming,
                    'convert_to_cai' => $this->convert_to_cai,
                    'sync_rest_to_clip' => $this->sync_rest_to_clip,
                    'format_the_mp4' => $this->format_the_mp4,
                    'normalized_the_mp4' => $this->normalized_the_mp4,
                    'generated_pngs' => $this->generated_pngs,
                    'fileinfo_created_at' => $this->fileinfo_created_at,
                    'sync_clip_to_rest' => $this->sync_clip_to_rest
        ];
    }
}
