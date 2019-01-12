<?php

namespace App\Providers;

use App\Events\FileInfoCreatedEvent;
use App\Listeners\ConvertToCaiListener;
use App\Listeners\GeneratePngsListener;
use App\Listeners\MediaLogger;
use App\Listeners\DispatchStatusOfFileInfoCreated;
use App\Listeners\FileInfoCreateListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
//            SendEmailVerificationNotification::class,
        ],
        'Spatie\MediaLibrary\Events\MediaHasBeenAdded' => [
            MediaLogger::class,
//            GeneratePngsListener::class,
            // FileInfoCreateListener::class,
            // MediaVideoConverterListener::class,

        ],
        'Spatie\MediaLibrary\Events\ConversionHasBeenCompleted' => [
            'App\Listeners\ConversionLogger',

        ],
//        FileInfoCreatedEvent::class => [
//           // DispatchStatusOfFileInfoCreated::class
//        ],
        // ClipConverted::class => [

        // ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

    }
}

