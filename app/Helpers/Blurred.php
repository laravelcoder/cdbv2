<?php
/**
 * Created by PhpStorm.
 * User: phillip.madsen
 * Date: 10/26/2018
 * Time: 2:54 PM
 */

namespace App\Helpers;

use Spatie\Image\Image;

class Blurred implements TinyPlaceholderGenerator
{
    public function generateTinyPlaceholder(string $sourceImagePath, string $tinyImageDestinationPath)
    {
        $sourceImage = Image::load($sourceImagePath);

        $sourceImage->width(32)->blur(5)->save($tinyImageDestinationPath);
    }
}
