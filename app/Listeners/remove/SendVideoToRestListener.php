<?php

namespace App\Listeners;

use App\Clip;
use Carbon\Carbon;
use FFMpeg\FFMpeg;
use Log;
use FFMpeg\FFProbe;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ixudra\Curl\Facades\Curl;

class SendVideoToRestListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public $clip;

    public function __construct(Clip $clip)
    {
        $this->clip = $clip;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        try {


            Log::debug($clip);

            Log::info("SYNCING LISTENER");
            $this->clip->update([
                'synced' => Carbon::now(),
            ]);
            Log::info("test- " . $clip);
            // dd($clip);
            // $data_json = json_encode($data);
            // $url = "https://adams.restapi.com/"
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $response  = curl_exec($ch);
                Log::info("LISTENER SYNCED");
            //     curl_close($ch);
        }
        catch (Exception $e) {
            //exception handling code goes here
        }
    }
}
