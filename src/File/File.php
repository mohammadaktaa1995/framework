<?php

namespace Wolfren\File;


class File
{
    private function __construct()
    {

    }


    /**
     * @return false|string
     */
    public static function root()
    {
        return ROOT;
    }

    /**
     * @return string
     */
    public static function ds()
    {
        return DS;
    }

    public static function path($path)
    {
        $path = static::root().static::ds().trim($path, '/');
        $path = str_replace(['/', '\\'], static::ds(), $path);

        return $path;
    }

    public static function exist($path)
    {
        return file_exists(static::path($path));
    }

    public static function require_file($path)
    {
        if (static::exist($path)) {
            return require_once static::path($path);
        }
    }

    public static function include_file($path)
    {
        if (static::exist($path)) {
            return include static::path($path);
        }
    }

    public static function require_dir($path)
    {
        if (is_dir(static::path($path))) {
            $files = array_diff(scandir(static::path($path)), ['.', '..']);
            foreach ($files as $file) {
                static::require_file($path.static::ds().$file);
            }
        }
    }
}