<?php

namespace App\Events;

use App\Clip;

use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\MediaLibrary\Models\Media;

class FileInfoCreatedEvent
{
    use Dispatchable, SerializesModels;

    public $clip;
    public $media;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Clip $clip, Media $media)
    {
        $this->clip = $clip;
        $this->media = $media;
    }

}
