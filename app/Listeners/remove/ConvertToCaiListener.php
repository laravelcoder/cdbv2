<?php

namespace App\Listeners;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\Events\ConversionHasBeenCompleted;
use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\Models\Media;
use App\Clip;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;


class ConvertToCaiListener
{

    /** @var \Spatie\MediaLibrary\Models\Media */
    public $media;

    public function __construct()
    {
        $this->clip = $media;
    }

    /**
     * Handle the event.
     *
     * @param  MediaHasBeenAdded  $event
     * @return void
     */
    public function handle(MediaHasBeenAdded $event)
    {
        \Log::info('MEDIA CONVERSION CONVERT TO CAI EVENT FIRED');
        $media = $event->media;
        \Log::error($media);
        $path = $media->getPath();
        \Log::error($path);

        ProcessConvertToCaiJob::dispatch($event->media);
    }
}
