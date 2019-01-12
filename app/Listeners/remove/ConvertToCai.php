<?php

namespace App\Listeners;

use App\Events\MediaHasBeenAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConvertToCai
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
        //
    }
}
