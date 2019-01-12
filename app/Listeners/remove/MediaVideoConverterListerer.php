<?php

namespace App\Listeners;

// use Spatie\MediaLibrary\Models\Media;
use App\Media;
use App\Clip;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;
use Spatie\MediaLibrary\Events\ConversionHasBeenCompleted;
use Spatie\MediaLibrary\Conversion\Conversion;

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
        if(getenv('CUSTOMDEBUG') === 'ON') {
            Log::info("Media::LISTENERHIT");
        }

        $this->media = $event->media;

        if(getenv('CUSTOMDEBUG') === 'ON') {
            Log::info("MediaVideoConverterListener STEP 1");
        }
        //prevent any events from media model
        $this->media->flushEventListeners();

        if(getenv('CUSTOMDEBUG') === 'ON') {
            Log::info("MediaVideoConverterListener STEP 2");
        }

        // Log::info("Media::ONE");
         // if ((!$this->isVideo())
         //     || $this->media->getCustomProperty('status') !== Media::MEDIA_STATUS_TO_CONVERT
         //     || strtolower($this->media->extension) == 'mp4' || strtolower($this->media->mime_type) == 'video/mp4'
         // ) {
         //    Log::info("Media::isVideo TRUE");
         //     $this->media->setCustomProperty('status', Media::MEDIA_STATUS_READY);
         //     $this->media->setCustomProperty('progress', 100);
         //     $this->media->save();

         //     //Log::info("ALREADY MP4: Media::MEDIA_STATUS_READY");

         //     return;
         // }

        // $this->media->setCustomProperty('status', Media::MEDIA_STATUS_PROCESSING);

        // Log::warning("Media::MEDIA_STATUS_PROCESSING");

        $this->media->save();

        if(getenv('CUSTOMDEBUG') === 'ON') {
                    Log::info("MediaVideoConverterListener STEP 3 Saved");
        }

        try {
            Log::info("Media:: TRY START");
            $fullPath = $this->media->getPath();
//            Log::info('FULLPATH: '. $this->media->getPath());
            $newFileFullPath = pathinfo($fullPath, PATHINFO_DIRNAME). DIRECTORY_SEPARATOR . pathinfo($fullPath, PATHINFO_FILENAME). Media::MEDIA_VIDEO_EXT;

          if(getenv('CUSTOMDEBUG') === 'ON') {
                       Log::info("FULLPATH: " . $fullPath . "| NEWFULLPATH: " .$newFileFullPath);
          }


            if (file_exists($newFileFullPath)) { unlink($newFileFullPath); }

                    if(getenv('CUSTOMDEBUG') === 'ON') {
                        Log::info("FFMpeg: ". config('medialibrary.ffmpeg_path'));
                        Log::info("FFProbe: ".  config('medialibrary.ffprobe_path'));
                    }


            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => config('medialibrary.ffmpeg_path'),
                'ffprobe.binaries' => config('medialibrary.ffprobe_path'),
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ]);

            //  'ffmpeg.configuration' => array(
            //     'ffmpeg.threads'   => 4,
            //     'ffmpeg.timeout'   => 300,
            //     'ffmpeg.binaries'  => '/opt/local/ffmpeg/bin/ffmpeg',
            //     'ffprobe.timeout'  => 30,
            //     'ffprobe.binaries' => '/opt/local/ffmpeg/bin/ffprobe',
            // ),
            // 'ffmpeg.logger' => $logger,

            $video = $ffmpeg->open($fullPath);

            $format = new X264();

            $format->on('progress', function ($video, $format, $percentage) use ($fullPath, $newFileFullPath) {
                if ($percentage >= 100) {
                    $this->mediaConvertingCompleted($fullPath, $newFileFullPath);
                    if(getenv('CUSTOMDEBUG') === 'ON') {
                        Log::info("MediaVideoConverterListener::MediaConvertingCompleted");
                    }
                } elseif (!($percentage % 10)) {
                    $this->media->setCustomProperty('progress', $percentage);
                    $this->media->save();
                    if(getenv('CUSTOMDEBUG') === 'ON') {
                        Log::info("MediaVideoConverterListener::MediaConvertingNOTCompleted");
                    }
                }
            });

           // $format->setAudioCodec(config('medialibrary.audio_codec', 'libvo_aacenc'))
           //     ->setKiloBitrate(1000)
           //     ->setAudioChannels(2)
           //     ->setAudioKiloBitrate(256);

            if(getenv('CUSTOMDEBUG') === 'ON') {
              Log::info("Converter Successful");
            }
            $video->save($format, $newFileFullPath);

            return;


        } catch (\Exception $e) {
                if(getenv('CUSTOMDEBUG') === 'ON') {
                    Log::info("Media::MEDIA_STATUS_FAILED");
                }
            $this->media->setCustomProperty('status', Media::MEDIA_STATUS_FAILED);
            $this->media->setCustomProperty('error', $e->getMessage());
            $this->media->save();
                if(getenv('CUSTOMDEBUG') === 'ON') {
                    Log::info("MediaVideoConverterListener::error", $e->getMessage());
                }
            return;
        }


    }

    /**
     * @param $originalFilePath
     * @param $convertedFilePath
     */
    protected function mediaConvertingCompleted($originalFilePath, $convertedFilePath)
    {
        if(getenv('CUSTOMDEBUG') === 'ON') {
                    Log::info("mediaConvertingCompleted:");
        }
        if (file_exists($originalFilePath)) {
            unlink($originalFilePath);
        }
        $this->media->file_name = pathinfo($convertedFilePath, PATHINFO_BASENAME);
        $this->media->mime_type = MediaLibraryFileHelper::getMimetype($convertedFilePath);
        $this->media->size = filesize($convertedFilePath);

        if(getenv('CUSTOMDEBUG') === 'ON') {
                    Log::info("CONVERTED FILEPATH: ".$convertedFilePath);
        }

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
        if(getenv('CUSTOMDEBUG') === 'ON') {
                    Log::info("isVideo::check");
        }

        return (strpos($this->media->mime_type, 'video') !== false);
    }


}
