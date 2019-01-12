<?php

namespace App\Listeners;

use App\Events\PNGsGeneratedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\GeneratePngsJob;
use App\Http\Controllers\Controller;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
// use FFMpeg;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\Point;
use FFMpeg\Media\Video;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Media\Frame;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Coordinate\AspectRatio;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;
use Storage;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Events\ConversionHasBeenCompleted;
use Spatie\MediaLibrary\Events\ConversionWillStart;
use App\Clip;

use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Carbon\Carbon;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use Illuminate\Support\Facades\File;


class GeneratePngsListener
{

    public function __construct()
    {

    }


    public function handle(MediaHasBeenAdded $event)
    {


//        Log::error($event);

        Log::error("GeneratePngsListener top");

        event(new PNGsGeneratedEvent($event));

        Log::error("GeneratePngsListener variables top");

        // GeneratePngsJob::dispatch($event->media);

        $mediaItems = $this->clip->getMedia('videos');

        $publicUrl = $mediaItems[0]->getUrl();
        $publicFullUrl = $mediaItems[0]->getFullUrl();
        $fullPathOnDisk = $mediaItems[0]->getPath();
        $clipPath = public_path('uploads/clips');

        $name = $mediaItems[0]->name;
        $size = $mediaItems[0]->human_readable_size;
        $file = $mediaItems[0]->file_name;
        $filename = $mediaItems[0]->file_name;
        $clipid = $mediaItems[0]->id;
        $slug = str_slug($name, '_');

        $fullPath = $clipPath . '/' . $slug;

        $fullPathOnDisk = $fullPath . '/' . $file;

        $storagePath = Storage::disk('clips')->getDriver()->getAdapter()->getPathPrefix();

        $file_name = pathinfo($storagePath . $slug . '/' . $file);
        $basename = $file_name['basename'];
        $extension = $file_name['extension'];
        $filename = $file_name['filename'];
        $tmpdir = str_slug($file_name['filename'], "_");
        $prefix = str_slug($tmpdir, "_");


        Log::error("GeneratePngsListener varials bottom");

        //        try {
        //
        //            if (!file_exists(public_path('uploads/clips/') . $slug . '/pngs/')) {
        //                File::makeDirectory(public_path('uploads/clips/') . $slug . '/pngs/', 0777, true);
        //            }
        //
        //            $destinationFolder = public_path('uploads/clips/') . $slug . '/pngs/';
        //
        //            exec('ffmpeg -i ' . $fullPathOnDisk . ' -vf fps=1 ' . $destinationFolder . '/' . $slug . '_%04d.jpg -hide_banner');
        //
        //            \Log::info("GeneratePngs Successfull");
        //
        //        } catch (\Exception $e) {
        //            Log::error($e->getMessage());
        //            Log::error("first try fail");
        //        }


//       try {
//
//           if (!file_exists(public_path('uploads/clips/') . $slug . '/cai/')) {
//               File::makeDirectory(public_path('uploads/clips/') . $slug . '/cai/', 0777, true);
//           }
//
//           $destinationCaiFolder = public_path('uploads/clips/') . $slug . '/cai/';
//
//           exec('ffmpeg -i ' . $fullPathOnDisk . ' -ac 1 -acodec pcm_s16le -ar 48000 ' . $destinationCaiFolder . '/' . $slug . '.pcm');
//
//           \Log::info("toCAI Successfull");
//
//       } catch (\Exception $e) {
//           Log::error($e->getMessage());
//           Log::error("2nd try fail");
//       }


        // $pngs = Storage::disk('clips')->files($slug . '/pngs/');

        //        foreach($pngs as $png) {
        //            ini_set("max_execution_time", 9999);
        //            ini_set("memory_limit", "2048M");
        //            $this->clip->addMedia(public_path('uploads/clips') . '/' . $png)->preservingOriginal()->toMediaCollection('images');
        //        }

        \Log::info("GeneratePngs Successfully");

        // $this->clip->update([
        //     'generated_pngs' => Carbon::now()
        // ]);

        Log::error("GeneratePngsListener bottom");

//        event(new PNGsGeneratedEvent($clip));
    }

}
