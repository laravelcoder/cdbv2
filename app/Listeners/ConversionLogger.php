<?php

namespace App\Listeners;

use Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\MediaLibrary\Events\ConversionHasBeenCompleted;
use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;

class ConversionLogger
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
    public function handle(ConversionHasBeenCompleted $event)
    {
//        \Log::info('MEDIA CONVERSION COMPLETED EVENT FIRED');
        $media = $event->media;
        $path = $media->getPath();
        \Log::info("CONVERSIONS have been completed on > {$media->name} < ad.");
    }
}
