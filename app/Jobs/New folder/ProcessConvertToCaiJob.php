<?php

namespace App\Jobs;

use App\Clip;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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


class ProcessConvertToCaiJob implements ShouldQueue
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
    public function handle($clip)
    {
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

        $media = \FFMpeg::fromDisk('videos')->open($clipid . '/'. $file);

        $clipdir = public_path('uploads/clips/'). $clipid;
        $clipwithpath = $clipdir . '/' . $file;

        $duration = $media->getDurationInMiliseconds();
        $durationInSeconds = $media->getDurationInSeconds();
        $durationInMiliseconds = $media->getDurationInMiliseconds();

        $filename = File::name($clipwithpath);
dd($filename);
        $media = FFMpeg::fromDisk('videos')->open($clipid . '/'. $file);


        \Log::info($media);
    }
}
