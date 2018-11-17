<?php
/**
 * Created by PhpStorm.
 * User: phillip.madsen
 * Date: 11/15/2018
 * Time: 2:59 PM
 */

namespace App\Helpers;

use Carbon\Carbon;

class Generators
{
    static function genSequencialNumber($str = '')
    {
        $number = $str;
        $number++;
        $str = "gui_". str_pad($number, 8, "0", STR_PAD_LEFT). "_" . Carbon::now()->timestamp;

        return $str;
    }

}
