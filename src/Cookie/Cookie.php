<?php


namespace Wolfren\Cookie;


class Cookie
{
    private function __construct()
    {
    }

    /**
     * Set Session
     *
     * @param $key
     * @param $value
     *
     * @return string
     */
    public static function set($key, $value, $expired = null)
    {
        $expired = $expired ?: time() + (1 * 365 * 24 * 60 * 60);
        setcookie($key, $value, $expired, '/', '', false, true);

        return $value;
    }


    /**
     * @param $key
     *
     * @return bool
     */
    public static function has($key)
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * @param  string  $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        return static::has($key) ? $_COOKIE[$key] : null;
    }

    /**
     * @param  string  $key
     *
     * @return void
     */
    public static function remove($key)
    {
        unset($_COOKIE[$key]);
        setcookie($key, null, '-1', '/');
    }

    /**
     * @return array
     */
    public static function all()
    {
        return $_COOKIE;
    }

    /**
     * @return void
     */
    public static function destroy()
    {
        foreach (static::all() as $key => $item) {
            static::remove($key);
        }
    }

}