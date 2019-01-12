<?php

namespace App\Listeners;

use App\Events\FileInfoCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Jobs\ConvertVideoForDownloading;
use App\Jobs\CreateFileInfo;
use App\Jobs\CreateFileInfoJob;
use App\Jobs\GeneratePngsJob;
use App\Jobs\MakeMP4;
use App\Jobs\ProcessConvertToCaiJob;
use App\Jobs\SendClipToRest;
use App\Jobs\ConvertVideoForStreaming;

class DispatchStatusOfFileInfoCreated
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
     * @param  FileInfoCreatedEvent  $event
     * @return void
     */
    public function handle(FileInfoCreatedEvent $event)
    {
        //Log::info("Job: FILEINFO CREATED " .  Carbon::now() . " FINISHED ");

        CreateFileInfoJob::dispatch($clip);

//      Mail::to($event->clip->user->email)->send(
//          new FileInfoCreated($event->clip)
//      );
    }
}
