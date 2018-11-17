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
 

		$this->addMediaConversion('screenshot')
		     ->width(368)
		     ->height(232)
		     ->extractVideoFrameAtSecond(20)
		     ->performOnCollections('videos');

        $this
            ->addMediaConversion('thumbs')
        	->performOnCollections('images')
            ->greyscale()
            ->sharpen(10)
            ->withResponsiveImages();

        $this->addMediaConversion('old-picture')
              ->sepia()
              ->performOnCollections('images')
              ->border(10, 'black', Manipulations::BORDER_OVERLAY);
    }
 
}
