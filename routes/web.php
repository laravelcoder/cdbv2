<?php

use App\Http\Resources\ClipResource;
use Illuminate\Http\UploadedFile;
use FFMpeg\FFMpeg as FFM;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\Point;
use FFMpeg\Media\Video;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Filters;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Format\Audio\DefaultAudio;
use FFMpeg\Format;
use Illuminate\Support\Facades\File;
use FFMpeg\Media\MediaTypeInterface;
use Illuminate\Contracts\Filesystem\Filesystem;

Route::get('/', function () { return redirect('/admin/home'); });

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login')->name('auth.login');
$this->post('logout', 'Auth\LoginController@logout')->name('auth.logout');

// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', 'HomeController@index');

    Route::resource('permissions', 'Admin\PermissionsController');
    Route::post('permissions_mass_destroy', ['uses' => 'Admin\PermissionsController@massDestroy', 'as' => 'permissions.mass_destroy']);
    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
    Route::resource('users', 'Admin\UsersController');
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
    Route::resource('clips', 'Admin\ClipsController');

    Route::get('clips/list',  ['uses' => 'Admin\ClipsController@list', 'as' => 'clips.list']);
    Route::post('clips_mass_destroy', ['uses' => 'Admin\ClipsController@massDestroy', 'as' => 'clips.mass_destroy']);
    Route::post('clips_restore/{id}', ['uses' => 'Admin\ClipsController@restore', 'as' => 'clips.restore']);
    Route::delete('clips_perma_del/{id}', ['uses' => 'Admin\ClipsController@perma_del', 'as' => 'clips.perma_del']);
    Route::post('/spatie/media/upload', 'Admin\SpatieMediaController@create')->name('media.upload');
    Route::post('/spatie/media/remove', 'Admin\SpatieMediaController@destroy')->name('media.remove');


    Route::get('servercheck', ['uses' => 'Admin\ClipsController@servercheck', 'as' => 'servercheck']);



});

// Route::get('welcome', function () {
//     return view('welcome');
// });

// Route::get('reports', 'ReportsController@index');


// Auth::loginUsingId(1);

// Route::post( 'video', function(){
// //   request()->file('video')->storeAs('videos', 'movie.mp4', ['disk'=> 'videos']);
// //    request()->file('video')->store('pirates');
//     $file = request()->file('video');
// $file->storeAs(auth()->id(), 'damovie.mp4',  ['disk'=> 'videos']);
//    // $media = \FFMpeg::open('damovie.mp4');
//     $durationInSeconds = $file->getDurationInSeconds(); // returns an int
//     $durationInMiliseconds = $file->getDurationInMiliseconds(); // returns a float
//     dd($durationInSeconds);
// //    $file = str_slug($file);
// //    dd($file);
// // //    $file->filters()->extractMultipleFrames(\FFMpeg\Filters\Video\ExtractMultipleFramesFilter::FRAMERATE_EVERY_5SEC, public_path('uploads/clips/'))->synchronize();
// //         //$file->save(new \FFMpeg\Format\Video\X264(), public_path('uploads/clips/'.$clip->id.'/'));
// //     $lowBitrate = (new X264)->setKiloBitrate(250);
// //     $highBitrate = (new X264)->setKiloBitrate(1000);
// //     \FFMpeg::open($file)
// //         ->exportForHLS()
// //         ->addFormat($lowBitrate, function($media) {
// //             $media->addFilter(function ($filters) {
// //                 $filters->resize(new \FFMpeg\Coordinate\Dimension(640, 480));
// //             });
// //         })
// //         ->addFormat($highBitrate, function($media) {
// //             $media->addFilter(function ($filters) {
// //                 $filters->resize(new \FFMpeg\Coordinate\Dimension(1280, 960));
// //             });
// //         })
// //         ->save('adaptive_steve.m3u8');
//     return back();
// });




Route::get( 'getcai', function()
{

});

//http://cdbv2/getpngs?http://d-gp2-caipyascs0-1.imovetv.com/ftp/downloads/nature-sounds-geico.mp4
// http://d-gp2-caipyascs0-1.imovetv.com/ftp/downloads/nature-sounds-geico.mp4
// http://cdbv2/getpngs?http://cdbv2/getpngs?http://d-gp2-caipyascs0-1.imovetv.com/ftp/downloads/coca-cola.mp4
Route::get( 'getpngs', function()
{

    $url = $_SERVER['QUERY_STRING'];
    $contents = file_get_contents($url);
    $name = substr($url, strrpos($url, '/') + 1);

    $file_name = pathinfo($name);
    $basename = $file_name['basename'];
    $extension = $file_name['extension'];
    $filename = $file_name['filename'];
    $tmpdir = str_slug($file_name['filename'], "_");
    $prefix = str_slug($tmpdir, "_");
    $name = $fixed_name = $prefix.'.'.$extension;
    $storagePath = Storage::disk('clips')->getDriver()->getAdapter()->getPathPrefix();
    $convertedPath = Storage::disk('clips')->getDriver()->getAdapter()->getPathPrefix();

    if (! file_exists($storagePath . $tmpdir . '/pngs/')) {
        File::makeDirectory($storagePath . $tmpdir . '/pngs/',0777, true);
    }


    // Storage::put($name, $contents);
    Storage::disk('clips')->put($name, $contents);

    $converted_directory = $storagePath . $tmpdir;

    $destinationFolder = $storagePath . $tmpdir . '/pngs/';

    // $link_to_mp4 = $converted_directory . '/' . $prefix . '.mp4';
    $converted = Storage::disk('clips')->exists($tmpdir. '/' . $prefix . '.mp4');

    $link_to_mp4 = $converted_directory . '/' . $prefix . '.mp4';

    exec('ffmpeg -y -i '. $storagePath . $name . ' -s 1280x720 -b:v 855k -vcodec libx264 -flags +loop+mv4 -movflags faststart -cmp 256 -partitions +parti4x4+parti8x8+partp4x4+partp8x8 -subq 6 -trellis 0 -refs 5 -bf 0 -coder 0 -me_range 16 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -qmin 10 -qmax 51 -qdiff 4 -c:a aac -strict -2 -ac 1 -ar 16000 -r 13 -ab 32000 -aspect 16:9 ' . $converted_directory . '/' . $prefix . '.mp4');

   exec('ffmpeg -i ' . $storagePath . $name . ' -vf fps=1 '. $destinationFolder. $prefix .'_%04d.jpg -hide_banner');

   $pngs = Storage::disk('clips')->files($tmpdir . '/pngs/');

   $pngurls = [];

    foreach($pngs as $png){
        $pngurls[] = url('/uploads/clips').'/'. $png;
    }

    if (file_exists($converted_directory . '/' . $name)) {
       Storage::disk('clips')->delete($name);
    }


   return Response::json([
            'clip' => [
                'name' => $name,
                'status' => 'success',
                'converted' => $converted,
                'source_video' => $url,
                'formatted_video' => url('/uploads/clips/'). $tmpdir. '/' . $prefix . '.mp4',
                'pngs' => $pngurls
             ]
         ],
            200
       );
});


Route::get('/pngs', function () {
    return response()->streamDownload(function () {
        echo file_get_contents($_SERVER['QUERY_STRING']);
    }, 'nice-name.jpg');
});

Event::listen('clip.change', function(){
    Cache::clear();
});
