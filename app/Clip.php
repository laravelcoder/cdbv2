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

    protected $fillable = ['videos', 'images', 'converted_for_downloading_at', 'converted_for_streaming_at'];
    protected $hidden = [];

    protected $dates = [
        'converted_for_downloading_at',
        'converted_for_streaming_at',
    ];


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

		$this->addMediaConversion('screenshot-5sec')
             ->width(368)
             ->height(232)
             ->sharpen(10)
             ->format('png')
            ->extractVideoFrameAtSecond(10)

		     ->performOnCollections('videos');

        $this->addMediaConversion('screenshot-10sec')
//             ->width(368)
//             ->height(232)
            ->extractVideoFrameAtSecond(10)->nonQueued()
            ->sharpen(10)
            ->format('png')
            ->watermark('watermark.png')
            ->watermarkOpacity(50)

            ->optimize()
            // ->useDisk('images')
            ->performOnCollections('videos');

        $this->addMediaConversion('screenshot-20sec')
//            ->width(368)
//            ->height(232)
            ->extractVideoFrameAtSecond(20)->nonQueued()
            ->sharpen(10)
            ->format('png')
            ->optimize()
            ->performOnCollections('videos');

        $this->addMediaConversion('screenshot-25sec')
//            ->width(368)
//            ->height(232)
            ->extractVideoFrameAtSecond(25)->nonQueued()
            ->sharpen(10)
            ->format('png')
            ->optimize()
            ->performOnCollections('videos');

        $this
            ->addMediaConversion('thumbs')
        	->performOnCollections('images')
            ->greyscale()
            ->sharpen(10)->nonQueued()
            ->format('png');
            //->withResponsiveImages()


        $this->addMediaConversion('old-picture')
              ->sepia()
              ->performOnCollections('images')->nonQueued()
              ->border(10, 'black', Manipulations::BORDER_OVERLAY);
    }
//    public function registerMediaConversions(Media $media = null)
//    {
//        $this->addMediaConversion('thumb')->width(400)->height(400)->extractVideoFrameAtSecond(10)->nonQueued();
//        $this->addMediaConversion('preview')->width(400)->height(400)->format('png')->extractVideoFrameAtSecond(10)->nonQueued();
//    }


}
