<?php

namespace App\Listeners;

use Spatie\MediaLibrary\Models\Media;
use App\Clip;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\Events\ConversionHasBeenCompleted;
use Spatie\MediaLibrary\Conversion\Conversion;

class ConvertToCaiConversionListener implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

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
        \Log::info('MEDIA CONVERSION CONVERT TO CAI EVENT FIRED');
         $media = $event->media;
        // \Log::error($media);
        $path = $media->getPath();
        \Log::error($path);
        // \Log::info("Conversions {$path} have been completed media {$media->id}");
    }
}
