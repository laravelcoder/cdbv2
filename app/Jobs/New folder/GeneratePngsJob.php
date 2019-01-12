<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
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
use App\Clip;
use \Log;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Carbon\Carbon;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use Illuminate\Support\Facades\File;


class GeneratePngsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $clip;

    public function __construct(Clip $clip)
    {
        $this->clip = $clip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("6. generated_pngs JOB | TOP");}


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

        $fullPath = $clipPath .'/' . $slug;

        $fullPathOnDisk = $fullPath .'/'. $file;

        $storagePath = Storage::disk('clips')->getDriver()->getAdapter()->getPathPrefix();

        $file_name = pathinfo($storagePath . $slug . '/'. $file);
        $basename = $file_name['basename'];
        $extension = $file_name['extension'];
        $filename = $file_name['filename'];
        $tmpdir = str_slug($file_name['filename'], "_");
        $prefix = str_slug($tmpdir, "_");

        $destinationFolder = public_path('uploads/clips/'). $slug . '/pngs/';

        if (! file_exists(public_path('uploads/clips/'). $slug. '/pngs/')) {
            File::makeDirectory(public_path('uploads/clips/'). $slug. '/pngs/',0777, true);
        }

        exec('ffmpeg -i ' . $fullPathOnDisk . ' -vf fps=1 '.$destinationFolder.'/'. $slug .'_%04d.jpg -hide_banner');

        $pngs = Storage::disk('clips')->files($clipid . '/pngs/');

//        foreach($pngs as $png) {
//            ini_set("max_execution_time", 9999);
//            ini_set("memory_limit", "2048M");
//            $this->clip->addMedia(public_path('uploads/clips') . '/' . $png)->preservingOriginal()->toMediaCollection('images');
//        }

        \Log::info("GeneratePngs Successfull");

        $this->clip->update([
            'generated_pngs' => Carbon::now()
        ]);

        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("6. generated_pngs JOB | BOTTOM");}
    }

}
