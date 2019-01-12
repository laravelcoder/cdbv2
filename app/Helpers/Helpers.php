<?php
/**
 * Created by PhpStorm.
 * User: phillip.madsen
 * Date: 10/17/2018
 * Time: 6:01 PM
 */

namespace App\Helpers;


class Helpers
{

    // Helpers::filelist();

    public static function filelist($dir) {

        $filesInFolder = \File::files($dir);

        foreach($filesInFolder as $path) {
              $file = pathinfo($path);
              dd($file['filename']);
              echo $file['filename'];
         }
    }
}
