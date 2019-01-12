<?php


return [

    'app_title' => 'Video Editor',
    'app_description' => 'Online Video Editor where you can cut, join and convert videos.',
    'lang' => 'en',
    'version' => '1.2.1',
    'base_url' => '/editor/public/',
    'home_url' => 'http://cdbv2/',
    'logo_image' => null,
    'root_path' => '/',
    'public_path' => '/public/',
    'input_dir' => 'userfiles/input/',
    'output_dir' => 'userfiles/output/',
    'tmp_dir' => 'userfiles/tmp/',
    'database_dir' => 'database/',
    'max_log_size' => 700 * 1024,
    'log_filename' => 'log.txt',
    'queue_size' => 5,
    'environment' => 'dev',
    'ffmpeg_path' => '/usr/bin/ffmpeg',
    'ffprobe_path' => '/usr/bin/ffprobe',
    'debug' => false,
    'upload_allowed' => array('mp4', 'm4v', 'flv', 'avi', 'mov', 'avi', 'mkv', 'mpg', 'webm', '3gp', 'ogv', 'mpg', 'wmv'),
    'upload_images' => array('jpg', 'jpeg', 'png'),
    'upload_audio' => array('mp3', 'm4a'),
    'watermark_text' => '',
    'watermark_text_font_name' => 'libel-suit-rg.ttf',
    'authentication' => false,
    'admin_auth_email' => false,
    'user_blocked_default' => false,
    'facebook_app_id' => 'xxxxxxxxxxxxxxxxxx',
    'facebook_secret_key' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'google_client_id' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com',
    'google_secret_key' => 'xxxxxxxxxxxxxxxxxx',

    'ffmpeg_string_arr' => [
        'flv' => '-c:v flv -b:v {quality} -c:a libmp3lame -b:a 128k -f {format}',
        'mp4' => '-c:v libx264 -b:v {quality} -c:a aac -strict experimental -b:a 128k -f {format}',
        'webm' => '-c:v libvpx -b:v {quality} -c:a libvorbis -b:a 128k -f {format}',
        'ogv' => '-c:v libtheora -b:v {quality} -c:a libvorbis -b:a 128k',
        'mp3' => '-vn -c:a libmp3lame -ab 192k -f {format}'
    ],

    'users_restrictions' => [
        'admin' => [
            'files_size_max' => 0,
            'show_log' => true
        ],
        'user' => [
            'files_size_max' => 300 * 1024 * 1024,
            'show_log' => true
        ],
    ]
];
