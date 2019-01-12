<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),

        ],

        'media' => [
            'driver'     => 'local',
            'root'       => public_path('uploads'),
            'url'        => env('APP_URL') . '/uploads',
            'visibility' => 'public',
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'uploads' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
        ],

        'uploads_test' => [
            'driver' => 'local',
            'root' => public_path('uploads/test'),
            'url'        => env('APP_URL') . '/uploads',
            'visibility' => 'public',
        ],

        'images' => [
            'driver' => 'local',
            'root'   => public_path('uploads/images'),
            'url'        => env('APP_URL') . '/uploads/images',
            'visibility' => 'public',
        ],

        'cai' => [
            'driver' => 'local',
            'root'   => public_path('uploads/cai'),
            'url'        => env('APP_URL') . '/uploads/cai',
            'visibility' => 'public',
        ],

        'thumbs' => [
            'driver' => 'local',
            'root'   => public_path('uploads/thumbs'),
            'url'        => env('APP_URL') . '/uploads/thumbs',
            'visibility' => 'public',
        ],

        'icons' => [
            'driver' => 'local',
            'root'   => public_path('uploads/icons'),
            'url'        => env('APP_URL') . '/uploads/icons',
            'visibility' => 'public',
        ],

        'streamable_videos' => [
            'driver' => 'local',
            'root'   => public_path('uploads/clips/feed'),
            'url'        => env('APP_URL') . '/uploads/clips/feed',
            'visibility' => 'public',
        ],

        'downloadable_videos' => [
            'driver' => 'local',
            'root'   => public_path('uploads/clips/down'),
            'url'        => env('APP_URL') . '/uploads/clips/down',
            'visibility' => 'public',
        ],

        'clips' => [
            'driver' => 'local',
            'root'   => public_path('uploads/clips'),
            'url'        => env('APP_URL') . '/uploads/clips',
            'visibility' => 'public',
        ],

        'videos' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'url'    => env('APP_URL') . '/media',

            'visibility' => 'public',
        ],

        'converted' => [
            'driver' => 'local',
            'root'   => storage_path('app/public'),
            'url'    => env('APP_URL') . '/converted/',

            'visibility' => 'public',
        ],

    ],

];
