<?php

namespace App\Jobs;

use App\Clip;
use App\Events\FileInfoCreated;
use App\Events\FileInfoCreatedEvent;
use App\Helpers\FFMPEG_helpers;
use Carbon\Carbon;
use Http\Client\Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Http\Request;
// use FFMpeg\FFMpeg;
// use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\Point;
use FFMpeg\Media\Video;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Coordinate\AspectRatio;
use Spatie\MediaLibrary\Models\Media;
use Storage;

class CreateFileInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $clip;

    public function __construct($clip)
    {
        $this->clip = $clip;
    }


    public function handle()
    {
        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("1. converted_for_downloading_at | TOP");}


        $mediaItems = $this->clip->getMedia('videos');
        $publicUrl = $mediaItems[0]->getUrl();
        $publicFullUrl = $mediaItems[0]->getFullUrl();
        $fullPathOnDisk = $mediaItems[0]->getPath();
        $clipPath = public_path('uploads/clips');

        $name = $mediaItems[0]->name;
        $size = $mediaItems[0]->human_readable_size;
        $file = $mediaItems[0]->file_name;
        $clipid = $mediaItems[0]->id;
        $slug = $title = str_slug($name, "_");

        $media = FFMpeg::fromDisk('clips')->open($slug . '/'. $file);

        // $clipdir = public_path('uploads/clips/'). $slug . '/other/';

        $duration = $media->getDurationInMiliseconds();
        $durationInSeconds = $media->getDurationInSeconds();
        $durationInMiliseconds = $media->getDurationInMiliseconds();
        //  $dimensions = $media->getStreams()->clips()->first()->getDimensions();

        /** debug: ============================================  */
        if(getenv('CUSTOMDEBUG') === 'ON') {
            Log::info("MediaFile::Duration: " . $duration);
            Log::info("MediaFile::durationInSeconds: " . $durationInSeconds);
            Log::info("MediaFile::durationInMiliseconds: " . $durationInMiliseconds);
            //Log::info("MediaFile::Dimensions: " . $dimensions);
        }

        $contents = [];
        $contents = "[FILE INFO]\n";
        $contents .= "ID: ". $this->id."\n";
        $contents .= "Name: ". $name."\n";
        $contents .= "DurationInSeconds: ". $durationInSeconds."\n";
        $contents .= "DurationInMiliseconds: ". $durationInMiliseconds."\n";
        $contents .= "Size: ". $size."\n";
        $contents .= "Filename: " . $file."\n";
        $contents .= "ClipID: " . $slug."\n";
        $contents .= "Slug: ". $slug."\n";
        $contents .= "Number of Generated Images: " . Storage::disk('clips')->files($clipid . '/pngs/')->count()."\n";

        if($slug){
            File::put(public_path('uploads/clips/'). $slug . '/FILEINFO.txt', $contents);

            event(new FileInfoCreatedEvent($this->clip));

            if(getenv('CUSTOMDEBUG') === 'ON') {
                Log::info("FILEINFO::WrittenSuccessfully");
            }
        }else{
            Log::info("FILEINFO::Not Written");
        }


        $this->clip->update([
            'fileinfo_created_at' => Carbon::now(),
            'title' => $name,
        ]);

        if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("1. converted_for_downloading_at | BOTTOM");}

    }

}

