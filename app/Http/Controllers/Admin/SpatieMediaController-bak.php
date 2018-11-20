<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Normalize;
use App\Helpers\FFMPEG_helpers;
use FFMpeg;
use File;
use FFMpeg\FFProbe;
use App\Helpers\Generators;

class SpatieMediaController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if (! $request->has('model_name') && ! $request->has('file_key') && ! $request->has('bucket')) {
            return abort(500);
        }

        $model = 'App\\' . $request->input('model_name');
        try {
            $model = new $model();
        } catch (ModelNotFoundException $e) {
            abort(500, 'Model not found');
        }

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

        $files      = $request->file($request->input('file_key'));
        $addedFiles = [];
        foreach ($files as $file) {


            try {
                $filename = $file->getClientOriginalName();

                if(preg_match('/^.*\.(mp4|mov|mpg|mpeg|wmv|mkv)$/i', $filename)){
                    Log::debug("VIDEO IDENTIFIED:");
                    $extension = $file->getClientOriginalExtension();

                    $filename = preg_replace('/--+/', '_', $filename);
                    $filename = str_replace(array('-',',','&'),'_', $filename);

                    $basename = substr($filename, 0, strrpos($filename, "."));
                    $basename = Normalize::titleCase($basename);
                    Log::debug("BASENAME: " .$basename);
                    $filename = str_slug($basename, '_'). '.'. $extension;
                    Log::debug("FILENAME: " .$filename);

                    $last = $model->latest()->first()->id;
                    $modelname = Generators::genSequencialNumber($last);
                    Log::debug("LAST: " .$last);
                    $daname = Generators::genSequencialNumber($last);
                    $dafullname = $daname .'.'. $extension;
                    Log::debug("MODELNAME: " .$modelname);
                    Log::debug("DANAME: " .$daname);
                    Log::debug("DAFULLNAME: " .$dafullname);

                    // Log::debug($model->addMedia($file)->usingName($basename)->usingFileName($filename)->preservingOriginal()->toMediaCollection($request->input('bucket')));
                    $model->exists     = true;

                    $media             = $model->addMedia($file)->usingName($basename)->usingFileName($dafullname)->preservingOriginal()->withResponsiveImages()->toMediaCollection($request->input('bucket'));


                    $addedFiles[]      = $media;
                    Log::debug('SUCCESS: ' .$addedFiles);

                }elseif(preg_match('/^.*\.(png|jpg|jpeg)$/i', $filename)){

                    Log::debug("IMAGE IDENTIFIED:");
                    $extension = $file->getClientOriginalExtension();

                    $filename = preg_replace('/--+/', '_', $filename);
                    $filename = str_replace(array('-',',','&'),'_', $filename);

                    $basename = substr($filename, 0, strrpos($filename, "."));
                    $basename = Normalize::titleCase($basename);
                    Log::debug("BASENAME: " .$basename);
                    $filename = str_slug($basename, '_'). '.'. $extension;
                    Log::debug("FILENAME: " .$filename);

                    $last = $model->latest()->first()->id;
                    $modelname = Generators::genSequencialNumber($last);

                    Log::debug("LAST: " .$last);
                    $daname = Generators::genSequencialNumber($last);
                    $dafullname = $daname .'.'. $extension;
                    Log::debug("MODELNAME: " .$modelname);
                    Log::debug("DANAME: " .$daname);
                    Log::debug("DAFULLNAME: " .$dafullname);

                    $model->exists     = true;

                    $media             = $model->addMedia($file)->usingName($basename)->usingFileName($dafullname)->preservingOriginal()->withResponsiveImages()->toMediaCollection($request->input('bucket'));

                    $addedFiles[]      = $media;
                    Log::debug('SUCCESS: ' .$addedFiles);
                }













            } catch (\Exception $e) {
                abort(500, 'Could not upload your file');
            }
        }

        return response()->json(['files' => $addedFiles]);
    }
}

// $number = $str;
// $number++;
// $str = "gui_". str_pad($number, 8, "0", STR_PAD_LEFT). "_" . Carbon::now()->timestamp;


