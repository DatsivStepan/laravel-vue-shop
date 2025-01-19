<?php

namespace App\Services;

class ThemeService
{
    const THEMES_PATH = '/resources/js/themes/*';

    /**
     * @return array
     */
    public static function getThemes()
    {
        $res = [];
        $files = glob(base_path() . self::THEMES_PATH, GLOB_ONLYDIR);
        foreach ($files as $file) {
            $baseName = basename($file);
            $res[$baseName] = ucfirst($baseName);
        }

        return $res;
    }
}
