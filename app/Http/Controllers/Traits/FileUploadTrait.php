<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use App\Helpers\Normalize;
use App\Helpers\FFMPEG_helpers;
use FFMpeg;
use FFMpeg\FFProbe;
use File;
use Carbon\Carbon;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\ImageGenerators\BaseGenerator;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Media;


trait FileUploadTrait
{

    /**
     * File upload trait used in controllers to upload files
     */
    public function saveFiles(Request $request)
    {

        Log::info("FILEUPLOADTRAIT::START");

        if (! file_exists(public_path('uploads'))) { File::makeDirectory(public_path('uploads'),0777, true);}
        if (! file_exists(public_path('uploads/icons'))) { File::makeDirectory(public_path('uploads/icons'),0777, true);}
        if (! file_exists(public_path('uploads/cai'))) { File::makeDirectory(public_path('uploads/cai'),0777, true); }
        if (! file_exists(public_path('uploads/clips'))) { File::makeDirectory(public_path('uploads/clips'),0777, true); }
        if (! file_exists(public_path('uploads/images'))) { File::makeDirectory(public_path('uploads/images'),0777, true); }
        if (! file_exists(public_path('uploads/thumbs'))) { File::makeDirectory(public_path('uploads/thumbs'),0777, true); }

        // $clipPath = config('gui.upload_path');
        $uploadPath = env('UPLOAD_PATH', 'uploads');
        $clipPath = env('CLIP_PATH', 'uploads/clips');
        $imagePath = env('IMAGE_PATH','uploads/images');
        $thumbPath = env('THUMB_PATH','uploads/thumbs');
        $caiPath = env('CAI_PATH','uploads/cai');
        $iconPath = env('ICON_PATH','uploads/icons');

        $getcai = env('CAI_SERVER');
        $transcoder = "/TOCAI.php?";
        $stamp = Carbon::now()->timestamp;
        $finalRequest = $request;

        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
                if ($request->has($key . '_max_width') && $request->has($key . '_max_height')) {
                    // Check file width
                    $filename = time() . '-' . $request->file($key)->getClientOriginalName();
                    $file     = $request->file($key);
                    $image    = Image::make($file);

                    Image::make($file)->resize(50, 50)->save($thumbPath . '/' . $filename);

                    $width  = $image->width();
                    $height = $image->height();
                    if ($width > $request->{$key . '_max_width'} && $height > $request->{$key . '_max_height'}) {
                        $image->resize($request->{$key . '_max_width'}, $request->{$key . '_max_height'});
                    } elseif ($width > $request->{$key . '_max_width'}) {
                        $image->resize($request->{$key . '_max_width'}, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } elseif ($height > $request->{$key . '_max_height'}) {
                        $image->resize(null, $request->{$key . '_max_height'}, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    $image->save($uploadPath . '/' . $filename);

                    Log::info("IF::HIT");

                    $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));

                } else {


                    shell_exec("ffmpeg -i ". $request->file ." -vf fps=1 " . public_path('uploads/test'). "/out%d.png");

                    $filename = time() . '-' . $request->file($key)->getClientOriginalName();
                    $request->file($key)->move($uploadPath, $filename);
                    $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));

                    Log::info("ELSE::HIT");

                }
            }
        }

        Log::info("FILEUPLOADTRAIT::END");

        Log::info($finalRequest);

        return $finalRequest;
    }

}
