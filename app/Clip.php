<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Manipulations;

/**
 * Class Clip
 *
 * @package App
*/
class Clip extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $fillable = ['videos', 'images'];
    protected $hidden = [];
   
	// public function registerMediaCollections()
	// {
	    // $this->addMediaCollection('videos')
	    //     ->acceptsFile(function (File $file) {
	    //         return $file->mimeType === 'video/mp4';
	    //     })
	    //     ->useDisk('videos')
	//         ->registerMediaConversions(function( Media $media = null){
 //                $this->addMediaConversion('video')
 //                    ->width(560)//480
 //                    ->height(315)//270
 //                    ->extractVideoFrameAtSecond(2)
 //                    ->extractVideoFrameAtSecond(20)
 //                    ->extractVideoFrameAtSecond(30)
 //                    // ->optimize()
 //                    ->nonQueued()
 //                    ->withResponsiveImages()
 //                    ->performOnCollections('videos');
 //            });

 //        $this->addMediaCollection('images')
 //            ->acceptsFile(function (File $file) {
	//             return $file->mimeType === 'image/jpeg';
	//         })
	//         ->useDisk('images')
	//         ->registerMediaConversions(function ( Media $media = null ) {
 //                $this->addMediaConversion('thumb')
 //                    ->useDisk('thumbs')
 //                    ->width(150)
 //                    ->height(120)
 //                    ->performOnCollections('videos', 'images')
 //                    ->nonQueued()
 //                    ->optimize()
 //                    ->withResponsiveImages();
 //            });
	// }

	public function registerMediaCollections()
	{
		$this->addMediaCollection('videos')
		    ->acceptsFile(function (File $file) {return $file->mimeType === 'video/mp4'; })
		    ->useDisk('videos');

		$this->addMediaCollection('images')
			->acceptsFile(function (File $file) {return $file->mimeType === 'image/jpeg'; })
			->useDisk('images');
	}



 	public function registerMediaConversions(Media $media = null)
    {
				$this->addMediaConversion('thumbs')
                    ->useDisk('thumbs')
                    ->width(150)
                    ->height(120)
                    ->performOnCollections('videos', 'images')
                    ->nonQueued()
                    ->optimize()
                    ->withResponsiveImages();
    }
    
}
