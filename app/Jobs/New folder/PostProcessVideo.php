<?php

namespace App\Jobs;

use Carbon\Carbon;
use FFMpeg;
use FFMpeg\Format\Video\WebM;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;

use App\Clip;

class PostProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Clip $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filename = File::name($this->video->path);

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

        // Get Duration and Resolution
        $duration = $media->getDurationInMiliseconds();
        $dimensions = $media->getStreams()->videos()->first()->getDimensions();

        // Grab a frame to use as a thumbnail from right about the middle of the video
        // This works: $media->getFrameFromString(date('H:i:s.v', ($duration/1000)/2))
        // This doesn't - it always sends 04:21:40.00 to ffmpeg:
        $media->getFrameFromSeconds(($duration/1000)/2)
            ->export()
            ->toDisk('public')
            ->save('videos/' . $filename . '_frame.png');

        // Convert to webm for streaming pleasure (or don't if it's already a webm)
        if($this->video->mimetype != 'video/webm') {
            $media->export()
                ->toDisk('public')
                ->inFormat(new WebM)
                ->save('videos/' . $filename . '.webm');
        }

        $this->video->update([
            'resolution' => $dimensions->getHeight(),
            'duration' => $duration,
            'streamable_path' => 'videos/' . $filename . '.webm',
            'thumbnail_path' => 'videos/' . $filename . '_frame.png',
            'postprocessing_completed_at' => Carbon::now(),
        ]);
    }
}
