<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class MediaLogger
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MediaHasBeenAdded $event)
    {
        $media = $event->media;
        $path = $media->getPath();
        if(getenv('CUSTOMDEBUG') === 1) {
                    Log::info("MEDIA:: {$media->id} {$media->name} PATH:: {$path} ");
        Log::info('MEDIA ADDED EVENT');
        }
    }



}
