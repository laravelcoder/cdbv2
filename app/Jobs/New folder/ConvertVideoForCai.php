<?php

namespace App\Jobs;

use Intervention\Image\Facades\Image;
use File;
use FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Log;
use App\Helpers\Normalize;
use App\Helpers\FFMPEG_helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\Media;
use App\Clip;
use Carbon\Carbon;

use FFMpeg\Format\Video\X264;

use Spatie\MediaLibrary\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\Helpers\File as MediaLibraryFileHelper;

class ConvertVideoForCai implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InteractsWithQueue, SerializesModels;

    public $video;
    protected $media;
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
if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("7. converted_to_cai JOB | TOP");}

        if (! file_exists(public_path().'/uploads')) { File::makeDirectory(public_path().'/uploads',0777, true);}
        if (! file_exists(public_path().'/uploads/cai')) { File::makeDirectory(public_path().'/uploads/cai',0777, true); }
        if (! file_exists(public_path().'/uploads/clips')) { File::makeDirectory(public_path().'/uploads/clips',0777, true); }
        if (! file_exists(public_path().'/uploads/images')) { File::makeDirectory(public_path().'/uploads/images',0777, true); }
        if (! file_exists(public_path().'/uploads/thumbs')) { File::makeDirectory(public_path().'/uploads/thumbs',0777, true); }

        // $clipPath = config('gui.upload_path');
        $uploadPath = env('UPLOAD_PATH', 'uploads');
        $clipPath = env('CLIP_PATH', 'uploads/clips');
        $imagePath = env('IMAGE_PATH','uploads/images');
        $thumbPath = env('THUMB_PATH','uploads/thumbs');
        $caiPath = env('CAI_PATH','uploads/cai');

        $getcai = env('CAI_SERVER');
        $transcoder = "/TOCAI.php?";

        $this->media = $event->media;
        //prevent any events from media model
        $this->media->flushEventListeners();

        if ((!$this->isVideo())
            || $this->media->getCustomProperty('status') !== Media::MEDIA_STATUS_TO_CONVERT
            || strtolower($this->media->extension) == 'mp4' || strtolower($this->media->mime_type) == 'video/mp4'
        ) {
            $this->media->setCustomProperty('status', Media::MEDIA_STATUS_READY);
            $this->media->setCustomProperty('progress', 100);
            $this->media->save();
            return;
        }

        $this->media->setCustomProperty('status', Media::MEDIA_STATUS_PROCESSING);
        $this->media->save();

        try{
            $fullPath = $this->video->getPath();

            $newFileFullPath = pathinfo($fullPath, PATHINFO_DIRNAME)
                . DIRECTORY_SEPARATOR . pathinfo($fullPath, PATHINFO_FILENAME)
                . Media::MEDIA_VIDEO_EXT;

            if (file_exists($newFileFullPath)) {
                unlink($newFileFullPath);
            }




        } catch (\Exception $e) {
            Log::info("Media::MEDIA_STATUS_FAILED");
            $this->video->setCustomProperty('status', Media::MEDIA_STATUS_FAILED);
            $this->video->setCustomProperty('error', $e->getMessage());
            $this->video->save();
        }

//            if ($this->video->hasFile()) {
//
//                try {
//                    Log::info("INIT CURL TOCAI");
//                    $ch = curl_init();
//                    // curl_setopt($ch,CURLOPT_URL,"". $getcai . $transcoder .  $file_w_path ."");
//                    curl_setopt($ch, CURLOPT_URL, "http://d-gp2-tocai-1.imovetv.com/TOCAI.php?http://d-gp2-caipyascs0-1.imovetv.com/ftp/downloads/coca-cola.mp4");
//                    curl_setopt($ch, CURLOPT_HEADER, 0);
//                    curl_setopt($ch, CURLOPT_POST, 1);
//                    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
//                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//
//                    $output = curl_exec($ch);
//                    file_put_contents($caiPath . "/" . str_slug($basename) . '.cai', $output);
//                    Log::info("SAVED CAI FILE");
//                    curl_close($ch);
//                } catch (Exception $e) {
//                    //exception handling code goes here
//                }
//            }

        $this->video->update([
            'converted_to_cai' => Carbon::now(),
        ]);

if(getenv('CUSTOMDEBUG') === 'ON') { Log::info("7. converted_to_cai JOB | BOTTOM");}

        }

}
