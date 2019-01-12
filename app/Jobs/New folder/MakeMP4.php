<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Clip;

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
use \Log;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use Illuminate\Support\Facades\File;




class MakeMP4 implements ShouldQueue
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

        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("4. format_the_mp4 JOB | TOP");}

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


        if (! file_exists(public_path('uploads/clips/'). $slug. '/converted/')) {
            File::makeDirectory(public_path('uploads/clips/'). $slug. '/converted/',0777, true);
        }

        $converted_directory = public_path('uploads/clips/'). $slug. '/converted/';

//        $rootpath = $fullPath. '/'.$file;
//        $rename = $fullPath .'/original.mp4';


       exec('ffmpeg -y -i '. $fullPathOnDisk . ' -s 1280x720 -b:v 855k -vcodec libx264 -flags +loop+mv4 -movflags faststart -cmp 256 -partitions +parti4x4+parti8x8+partp4x4+partp8x8 -subq 6 -trellis 0 -refs 5 -bf 0 -coder 0 -me_range 16 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -qmin 10 -qmax 51 -qdiff 4 -c:a aac -strict -2 -ac 1 -ar 16000 -r 13 -ab 32000 -aspect 16:9 ' . $converted_directory . '/' . $prefix . '.mp4');

        // dd($storagePath. $slug.'/'.$file);
//        $oldfile = $storagePath. $clipid.'/'.$file;
//        $newfile =  $storagePath. $clipid.'/renamed/old.mp4';





        //dd($newfile);
         // Storage::disk('clips')->move($rootpath, $rename);
        // Storage::::disk('clips')->move($converted_directory .  $prefix . '.mp4', 'new/file.jpg');

//        Storage::copy($oldfile,$newfile);


        $this->clip->update([
            'format_the_mp4' => Carbon::now(),
            'normalized_the_mp4' => Carbon::now(),
        ]);

        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("4. format_the_mp4 JOB | BOTTOM");}
    }
}
