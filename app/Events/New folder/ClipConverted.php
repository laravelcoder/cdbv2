<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ClipConverted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $clip;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($clip)
    {
        $this->clip = $clip;
    }

}
