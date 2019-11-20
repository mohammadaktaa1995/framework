<?php


namespace Wolfren\Http;


class Server
{
    private function __construct()
    {

    }

    /**
     * @return array
     */
    public static function all()
    {
        return $_SERVER;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SERVER[$key]);
    }

    /**
     * @param $key
     *
     * @return string
     */
    public static function get($key)
    {
        return static::has($key) ? $_SERVER[$key] : null;
    }

    /**
     * @param $path
     *
     * @return array
     */
    public static function path_info($path)
    {
        return pathinfo($path);
    }
}