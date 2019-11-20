<?php


namespace PhpFramework\Session;


class Session
{
    private function __construct()
    {
    }

    /**
     * Session start
     */

    public static function start()
    {
        if (! session_id()) {
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    /**
     * Set Session
     *
     * @param $key
     * @param $value
     *
     * @return string
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;

        return $value;
    }


    /**
     * @param $key
     *
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @param  string  $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        return static::has($key) ? $_SESSION[$key] : null;
    }

    /**
     * @param  string  $key
     *
     * @return void
     */
    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @return array
     */
    public static function all()
    {
        return $_SESSION;
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


    /**
     * Flash Session
     */

    public static function flash($key)
    {
        $value = null;
        if (static::has($key)) {
            $value = static::get($key);
            static::remove($key);
        }

        return $value;

    }


}