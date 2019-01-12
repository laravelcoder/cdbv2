<?php

namespace App\Jobs;

use App\Clip;
use Carbon\Carbon;
use FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\Point;
use FFMpeg\Media\Video;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\FrameRate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use File;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;
use Log;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Clip $video)
    {
        $this->video = $video;
    }

    public function handle()
    {
        // create some video formats...
        $lowBitrateFormat  = (new X264('libmp3lame'))->setKiloBitrate(500);
        $midBitrateFormat  = (new X264('libmp3lame'))->setKiloBitrate(1500);
        $highBitrateFormat = (new X264('libmp3lame'))->setKiloBitrate(3000);

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



// try{
        $contents = [];
        $contents = "[FILE INFO]\n";
        $contents .= "Name: ". $name;
        $contents .= "Size: ". $size;
        $contents .= "Filename: " . $file;
        $contents .= "ClipID: " . $clipid;
        $contents .= "Slug: ". $slug;

        File::put(public_path('uploads/clips/'). $clipid . '/FILEINFO.txt', $contents);
        Log::info("MediaFile::WrittenSuccessfully");

        $video = FFMpeg::fromDisk('videos')->open($clipid . '/'. $file)
            ->addFilter(function ($filters) {
                $filters->resize(new Dimension(1920, 1080));
            })->export()
            ->toDisk('downloadable_videos')
            ->inFormat($highBitrateFormat)
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
            ->save($slug ."/". $slug . '.mp4');

            function extractImages( $video, $duration, $n ){
                $interval = floor( $duration / $n );
                for($i = 0; $i < $n; $i++){
                    $frame = $video->frame(TimeCode::fromSeconds( $i * $interval ));
                    $frame->save(public_path('uploads/clips/frame-' . $i . '.jpg'));
                }
            }

        // open the uploaded video from the right disk...
        FFMpeg::fromDisk('videos')
            ->open($this->Video)

        // call the 'exportForHLS' method and specify the disk to which we want to export...
            ->exportForHLS()
            ->toDisk('streamable_videos')

        // we'll add different formats so the stream will play smoothly
        // with all kinds of internet connections...
            ->addFormat($lowBitrateFormat)
            ->addFormat($midBitrateFormat)
            ->addFormat($highBitrateFormat)


        // call the 'save' method with a filename...
            ->save($slug ."/". $slug . '.m3u8');

        // update the database so we know the convertion is done!
        $this->video->update([
            'converted_for_streaming_at' => Carbon::now(),
        ]);

        Log::info("Job: converted_for_streaming_at " .  Carbon::now() . " FINISHED ");


    }
}
