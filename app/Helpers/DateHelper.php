<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * @param string $string
     * @return string
     */
    public static function dateFormat($string, $format = 'Y-m-d H:i:s')
    {
        $res = '';

        if ($string) {
            $res = Carbon::createFromFormat($format, $string)->format(__('langconfig.iso8601'));
        }

        return $res;
    }
}