<?php

namespace App\Jobs;

use App\Clip;
use Carbon\Carbon;
// use FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use File;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;
use Log;


class SendClipToRest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $clip;


    public function __construct(Clip $clip)
    {
        $this->clip = $clip;

    }

    public function handle()
    {
        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("9. sync_clip_to_rest JOB | TOP");}

        // Log::info("SYNCED MEDIA JOB: " . $media);
        // $clip = $this->clip;

        Log::info("SYNCED JOB: " . $this->clip);

        try {
            Log::info("SYNCING JOB");
           // $data = $this->clip;

            $this->clip->update([
                'synced' => Carbon::now(),
                'sync_clip_to_rest' => Carbon::now(),
            ]);

            Log::info("Job: test- " . $this->clip);
            //dd($clip);
            // $data_json = json_encode($data);
            // $url = "https://adams.restapi.com/"
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $response  = curl_exec($ch);
            //     Log::info("SYNCED");
            //     curl_close($ch);
        }
        catch (Exception $e) {
            Log::error("SYNCING FAILED");
        }


        // update the database so we know the convertion is done!

        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("9. sync_clip_to_rest JOB | BOTTOM");}

    }
}

