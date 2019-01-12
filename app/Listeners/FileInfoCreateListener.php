<?php

namespace App\Listeners;


use App\Jobs\CreateFileInfoJob;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Clip;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Support\Facades\File;

class FileInfoCreateListener
{


    /**
     * Handle the event.
     *
     * @param  MediaHasBeenAdded  $event
     * @return void
     */
    public function handle(MediaHasBeenAdded $event)
    {
        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("8. fileinfo_created_a LISTENER | TOP");}

        $clip = $event->clip;

        CreateFileInfoJob::dispatch($clip);

        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("8. fileinfo_created_at LISTENER | BOTTOM");}

    }

}
