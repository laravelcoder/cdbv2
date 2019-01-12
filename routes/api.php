<?php

use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {

        Route::resource('clips', 'ClipsController', ['except' => ['create', 'edit']]);

        // Route::get('getclip/{clip}', function ($clip){
        //     $client = new \Guzzle\Service\Client('http://adamlink.com/');
        //     $response = $client->get("clips/$clip")
        // })

    Route::get( 'getpngs', function()
    {

        $url = $_SERVER['QUERY_STRING'];
        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);

        $file_name = pathinfo($name);
        $basename = $file_name['basename'];
        $extension = $file_name['extension'];
        $filename = $file_name['filename'];
        $tmpdir = str_slug($file_name['filename'], "_");
        $prefix = str_slug($tmpdir, "_");
        $name = $fixed_name = $prefix.'.'.$extension;
        $storagePath = Storage::disk('clips')->getDriver()->getAdapter()->getPathPrefix();
        $convertedPath = Storage::disk('clips')->getDriver()->getAdapter()->getPathPrefix();

        if (! file_exists($storagePath . $tmpdir . '/pngs/')) {
            File::makeDirectory($storagePath . $tmpdir . '/pngs/',0777, true);
        }


        // Storage::put($name, $contents);
        Storage::disk('clips')->put($name, $contents);

        $converted_directory = $storagePath . $tmpdir;

        $destinationFolder = $storagePath . $tmpdir . '/pngs/';

        // $link_to_mp4 = $converted_directory . '/' . $prefix . '.mp4';
        $converted = Storage::disk('clips')->exists($tmpdir. '/' . $prefix . '.mp4');

        $link_to_mp4 = $converted_directory . '/' . $prefix . '.mp4';

        exec('ffmpeg -y -i '. $storagePath . $name . ' -s 1280x720 -b:v 855k -vcodec libx264 -flags +loop+mv4 -movflags faststart -cmp 256 -partitions +parti4x4+parti8x8+partp4x4+partp8x8 -subq 6 -trellis 0 -refs 5 -bf 0 -coder 0 -me_range 16 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -qmin 10 -qmax 51 -qdiff 4 -c:a aac -strict -2 -ac 1 -ar 16000 -r 13 -ab 32000 -aspect 16:9 ' . $converted_directory . '/' . $prefix . '.mp4');

       exec('ffmpeg -i ' . $storagePath . $name . ' -vf fps=1 '. $destinationFolder. $prefix .'_%04d.jpg -hide_banner');

       $pngs = Storage::disk('clips')->files($tmpdir . '/pngs/');

       $pngurls = [];
        foreach($pngs as $png){
            $pngurls[] = url('/uploads/clips').'/'. $png;
        }

        if (file_exists($converted_directory . '/' . $name)) {
           Storage::disk('clips')->delete($name);
        }


       return Response::json([
                'clip' => [
                    'name' => $name,
                    'status' => 'success',
                    'converted' => $converted,
                    'source_video' => $url,
                    'formatted_video' => url('/uploads/clips/'). $tmpdir. '/' . $prefix . '.mp4',
                    'pngs' => $pngurls
                 ]
             ],
                200
           );
    });

});
