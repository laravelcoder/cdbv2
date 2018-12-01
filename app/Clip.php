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

    protected $fillable = ['videos', 'images', 'converted_for_downloading_at', 'converted_for_streaming_at','converted_to_cai'];
    protected $hidden = [];

    protected $dates = [
        'converted_for_downloading_at',
        'converted_for_streaming_at',
        'converted_to_cai',
    ];


	public function registerMediaCollections()
	{
		$this->addMediaCollection('videos')
		    ->acceptsFile(function (File $file) {return $file->mimeType === 'video/mp4'; })
		    ->useDisk('videos')
            ->singleFile();

		$this->addMediaCollection('images')
			->acceptsFile(function (File $file) {return $file->mimeType === 'image/jpeg' || $file->mimeType === 'image/png'; })
			->useDisk('images');


	}

    public function registerMediaConversions(Media $media = null)
    {

// 1.5862068965517 : 1

        // $this->addMediaConversion('screenshot-5sec')->extractVideoFrameAtSecond(5)->performOnCollections('videos')->width(368)->height(232)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-10sec')->extractVideoFrameAtSecond(10)->performOnCollections('videos')->width(368)->height(232)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-15sec')->extractVideoFrameAtSecond(15)->performOnCollections('videos')->width(368)->height(232)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-20sec')->extractVideoFrameAtSecond(20)->performOnCollections('videos')->width(368)->height(232)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-25sec')->extractVideoFrameAtSecond(25)->performOnCollections('videos')->width(368)->height(232)->sharpen(10)->nonQueued()->format('png');

        $this->addMediaConversion('screenshot-5sec')->extractVideoFrameAtSecond(5)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');
        $this->addMediaConversion('screenshot-10sec')->extractVideoFrameAtSecond(10)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');
        $this->addMediaConversion('screenshot-15sec')->extractVideoFrameAtSecond(15)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');
        $this->addMediaConversion('screenshot-20sec')->extractVideoFrameAtSecond(20)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');
        $this->addMediaConversion('screenshot-25sec')->extractVideoFrameAtSecond(25)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');

        $this->addMediaConversion('thumbs')
        	->performOnCollections('images')
            ->greyscale()
            ->sharpen(10)
            ->nonQueued()
            ->format('png');
            //->withResponsiveImages()


        $this->addMediaConversion('old-picture')
              ->sepia()
              ->performOnCollections('images')
              ->sharpen(10)
//              ->nonQueued()
              ->border(10, 'black', Manipulations::BORDER_OVERLAY);
    }
//    public function registerMediaConversions(Media $media = null)
//    {
//        $this->addMediaConversion('thumb')->width(400)->height(400)->extractVideoFrameAtSecond(10)->nonQueued();
//        $this->addMediaConversion('preview')->width(400)->height(400)->format('png')->extractVideoFrameAtSecond(10)->nonQueued();
//    }


}
