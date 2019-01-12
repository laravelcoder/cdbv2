<?php

namespace App\Listeners;

use App\Events\MediaHasBeenAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneratePngs
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
     * @param  MediaHasBeenAdded  $event
     * @return void
     */
    public function handle(MediaHasBeenAdded $event)
    {
        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("6. generated_pngs LISTENER | TOP");}
        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("6. generated_pngs LISTENER | BOTTOM");}
    }
}
