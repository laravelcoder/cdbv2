<?php

namespace App\Jobs;

use App\Clip;
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



class ConvertVideoForDownloading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Clip $video)
    {
        $this->video = $video;
    }

    public function handle()
    {
        // create a video format...
        $lowBitrate = (new X264)->setKiloBitrate(50)->setAudioCodec('aac');
        $midBitrate = (new X264)->setKiloBitrate(150)->setAudioCodec('aac');
        $highBitrate = (new X264)->setKiloBitrate(300)->setAudioCodec('aac');

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

       // $media = FFMpeg::fromDisk('videos')->open($clipid . '/'. $file);
        $clipdir = public_path('uploads/clips/'). $clipid . '/other/';


        // open the uploaded video from the right disk...
//        FFMpeg::fromFilesystem($this->video->disk)
        FFMpeg::fromDisk('media')->open($this->video)

        // add the 'resize' filter...
            ->addFilter(function ($filters) {
                $filters->resize(new Dimension(432, 320));
            })

        // call the 'export' method...
            ->export()

        // tell the MediaExporter to which disk and in which format we want to export...
            ->toDisk('downloadable_videos')
            ->inFormat($midBitrate)

        // call the 'save' method with a filename...
            ->save(public_path('uploads/clips/'). $clipid ."/holymoly/". $slug . '.mp4');

        // update the database so we know the convertion is done!
        $this->video->update([
            'converted_for_downloading_at' => Carbon::now(),
        ]);
    }
}

