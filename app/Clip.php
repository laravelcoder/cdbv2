<?php
namespace App;

use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
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

    protected $fillable =
        [
            'title',
            'videos',
            'images',
            'percentage',
            'creator',
            'sync_rest_to_clip',
            'format_the_mp4',
            'normalized_the_mp4',
            'generated_pngs',
            'converted_to_cai',
            'fileinfo_created_at',
            'sync_clip_to_rest'
        ];

    protected $hidden = [];


    protected $dates = [
        'converted_for_downloading_at',
        'converted_for_streaming_at',
        'converted_to_cai',
        'synced',
        'sync_clip_to_rest',
        'fileinfo_created_at',
        'normalized_mp4',
        'generated_pngs'
    ];



    protected $dispatchesEvents = [

    ];

//    public function toArray()
//    {
//        return [
//            'videos' => $this->getMedia('videos')
//        ];
//    }

	public function registerMediaCollections()
	{
		$this->addMediaCollection('videos')
		    ->acceptsFile(function (File $file) {return $file->mimeType === 'video/mp4'; })
		    ->useDisk('clips')
            //->width(368)->height(232)
            ->singleFile();

		$this->addMediaCollection('images')
			->acceptsFile(function (File $file) {return $file->mimeType === 'image/jpeg' || $file->mimeType === 'image/png'; })
			->useDisk('images');


	}

    public function registerMediaConversions(Media $media = null)
    {
            $this->addMediaConversion('video')->performOnCollections('videos')->nonQueued();

            $this->addMediaConversion('thumb')->width(368)->height(232)->performOnCollections('images')->greyscale()->sharpen(10)->nonQueued()->format('png');

        $this->addMediaConversion('screenshot-5sec')->extractVideoFrameAtSecond(5)->performOnCollections('videos')->width(432)->height(320)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-10sec')->extractVideoFrameAtSecond(10)->performOnCollections('videos')->width(432)->height(320)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-15sec')->extractVideoFrameAtSecond(15)->performOnCollections('videos')->width(432)->height(320)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-20sec')->extractVideoFrameAtSecond(20)->performOnCollections('videos')->width(432)->height(320)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-25sec')->extractVideoFrameAtSecond(25)->performOnCollections('videos')->width(432)->height(320)->sharpen(10)->nonQueued()->format('png');

        // $this->addMediaConversion('screenshot-5sec')->extractVideoFrameAtSecond(5)->performOnCollections('videos')->width(432)->height(320)->sharpen(10)->nonQueued()->format('png');
        // $this->addMediaConversion('screenshot-10sec')->extractVideoFrameAtSecond(10)->performOnCollections('videos')->width(432)->height(320)->sharpen(10)->nonQueued()->format('png');
//        $this->addMediaConversion('screenshot-15sec')->extractVideoFrameAtSecond(15)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');
//        $this->addMediaConversion('screenshot-20sec')->extractVideoFrameAtSecond(20)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');
//        $this->addMediaConversion('screenshot-25sec')->extractVideoFrameAtSecond(25)->performOnCollections('videos')->sharpen(10)->nonQueued()->format('png');

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

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator', 'id');
    }


}
