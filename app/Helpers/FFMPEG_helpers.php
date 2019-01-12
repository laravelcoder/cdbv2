<?php
/**
 * Created by PhpStorm.
 * User: phillip.madsen
 * Date: 10/17/2018
 * Time: 6:32 PM
 * https://stackoverflow.com/questions/45072598/how-to-get-duration-size-and-dimensions-of-video-file-input-in-yii2
 */

namespace App\Helpers;


class FFMPEG_helpers
{
    public static function getDuration($filePath = '')
    {
        exec('ffmpeg -i'." '$filePath' 2>&1 | grep Duration | awk '{print $2}' | tr -d ,",$O,$S);
        if(!empty($O[0]))
        {
            return $O[0];
        }else
        {
            return false;
        }
    }

    public static function getAVWidthHeight($filePath = '')
    {
        exec("ffprobe -v error -show_entries stream=width,height -of default=noprint_wrappers=1 '$filePath'",$O,$S);
        if(!empty($O))
        {
            $list = [
                    "width"=>explode("=",$O[0])[1],
                    "height"=>explode("=",$O[1])[1],
            ];

            return $list;
        }else
        {
            return false;
        }
    }

    /**
     * FFMPEG_helpers:: extractImages($video, $durationInSeconds, $n )
     * @param $video
     * @param $durationInSeconds
     * @param $n
     */
    public static function extractImages($video, $durationInSeconds, $n ){

        $interval = floor( $durationInSeconds / $n );
        for($i = 0; $i < $n; $i++){
            $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds( $i * $interval ));
            //$frame->addMedia('/'. $clipid .'_out%d.png')->toMediaCollection('images');
            $frame->save( public_path('uploads/test/'). 'pip-' . $i . '.jpg');
        }
    }
}
