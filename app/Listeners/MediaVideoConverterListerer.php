<?php

namespace App\Listeners;

use Spatie\MediaLibrary\Models\Media;
use App\Clip;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;
use Illuminate\Support\Facades\Log;


class MediaVideoConverterListener implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    protected $media;

    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MediaHasBeenAdded $event)
    {
        Log::info("Media::LISTENERHIT");
        $this->media = $event->media;
        Log::info("STEP 1");
        //prevent any events from media model
        $this->media->flushEventListeners();
        Log::info("STEP 2");

         if ((!$this->isVideo())
             || $this->media->getCustomProperty('status') !== Media::MEDIA_STATUS_TO_CONVERT
             || strtolower($this->media->extension) == 'mp4' || strtolower($this->media->mime_type) == 'video/mp4'
         ) {
             $this->media->setCustomProperty('status', Media::MEDIA_STATUS_READY);
             $this->media->setCustomProperty('progress', 100);
             $this->media->save();

             Log::info("ALREADY MP4: Media::MEDIA_STATUS_READY");

             return;
         }

        $this->media->setCustomProperty('status', Media::MEDIA_STATUS_PROCESSING);

        Log::warning("Media::MEDIA_STATUS_PROCESSING");

        $this->media->save();

        try {
            $fullPath = $this->media->getPath();
//            Log::info($this->media->getPath());
            $newFileFullPath = pathinfo($fullPath, PATHINFO_DIRNAME). DIRECTORY_SEPARATOR . pathinfo($fullPath, PATHINFO_FILENAME). Media::MEDIA_VIDEO_EXT;

//            Log::info("FULLPATH: " . $fullPath . ", NEWFULLPATH: " .$newFileFullPath);


            if (file_exists($newFileFullPath)) {
                unlink($newFileFullPath);
            }

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => config('medialibrary.ffmpeg_binaries'),
                'ffprobe.binaries' => config('medialibrary.ffprobe_binaries'),
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ]);

            $video = $ffmpeg->open($fullPath);

            $format = new X264();

            $format->on('progress', function ($video, $format, $percentage) use ($fullPath, $newFileFullPath) {
                if ($percentage >= 100) {
                    $this->mediaConvertingCompleted($fullPath, $newFileFullPath);
                } elseif (!($percentage % 10)) {
                    $this->media->setCustomProperty('progress', $percentage);
                    $this->media->save();
                }
            });

           $format->setAudioCodec(config('medialibrary.audio_codec', 'libvo_aacenc'))
               ->setKiloBitrate(1000)
               ->setAudioChannels(2)
               ->setAudioKiloBitrate(256);

//            Log::info("Converter Successful");
            $video->save($format, $newFileFullPath);

            return;


        } catch (\Exception $e) {
//            Log::info("Media::MEDIA_STATUS_FAILED");
            $this->media->setCustomProperty('status', Media::MEDIA_STATUS_FAILED);
            $this->media->setCustomProperty('error', $e->getMessage());
            $this->media->save();

            return;
        }


    }

    /**
     * @param $originalFilePath
     * @param $convertedFilePath
     */
    protected function mediaConvertingCompleted($originalFilePath, $convertedFilePath)
    {
        Log::info("mediaConvertingCompleted:");
        if (file_exists($originalFilePath)) {
            unlink($originalFilePath);
        }
        $this->media->file_name = pathinfo($convertedFilePath, PATHINFO_BASENAME);
        $this->media->mime_type = MediaLibraryFileHelper::getMimetype($convertedFilePath);
        $this->media->size = filesize($convertedFilePath);
//        Log::info("CONVERTED FILEPATH: ".$convertedFilePath);
        $this->media->setCustomProperty('status', Media::MEDIA_STATUS_READY);
        $this->media->setCustomProperty('progress', 100);
        $this->media->save();

        return;

    }


    /**
     * Is media a video?
     *
     * @return bool
     */
    protected function isVideo()
    {
        Log::info("isVideo::check");
        return (strpos($this->media->mime_type, 'video') !== false);
    }


}
