<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Clip;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;

class PNGsGeneratedEvent
{
    use SerializesModels;

    public $clip;

    /**
     * Create a new event instance.
     * @param  \App\Clip  $clip
     * @return void
     */
    public function __construct(Media $clip)
    {
        $this->clip = $clip;

    }

}
