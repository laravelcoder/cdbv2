<?php
/**
 * Created by PhpStorm.
 * User: phillip.madsen
 * Date: 10/26/2018
 * Time: 2:54 PM
 */

namespace App\Helpers;


class Custom
{

    /**
     * Shortens a string in a pretty way. It will clean it by trimming
     * it, remove all double spaces and html. If the string is then still
     * longer than the specified $length it will be shortened. The end
     * of the string is always a full word concatenated with the
     * specified moreTextIndicator.
     *
     * @param string $string
     * @param int    $length
     * @param string $moreTextIndicator
     *
     * @return string
     */
    function str_tease(string $string, int $length = 200, string $moreTextIndicator = '...'): string
    {
        $string = trim($string);
        //remove html
        $string = strip_tags($string);
        //replace multiple spaces
        $string = preg_replace("/\s+/", ' ', $string);
        if (strlen($string) == 0) {
            return '';
        }
        if (strlen($string) <= $length) {
            return $string;
        }
        $ww = wordwrap($string, $length, "\n");
        $string = substr($ww, 0, strpos($ww, "\n")) . $moreTextIndicator;
        return $string;
    }

}
