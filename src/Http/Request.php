<?php


namespace Wolfren\Http;


class Request
{

    /**
     * @var $script_name
     */
    private static $script_name;
    /**
     * @var $base_url
     */
    private static $base_url;
    /**
     * @var $url
     */
    private static $url;
    /**
     * @var $full_url
     */
    private static $full_url;
    /**
     * @var $query_string
     */
    private static $query_string;

    private function __construct()
    {

    }

    public static function handle()
    {
        static::$script_name = str_replace('\\', '', dirname(Server::get('SCRIPT_NAME')));
        $protocol = Server::get('REQUEST_SCHEME').'://';
        $host = Server::get('HTTP_HOST');
        $scrip_name = static::$script_name;
        static::setBaseUrl($protocol.$host.$scrip_name);

        $request_uri = urldecode(Server::get('REQUEST_URI'));
        $request_uri = rtrim(preg_replace('#^'.static::$script_name.'#', '', $request_uri), '/');
        static::setFullUrl($request_uri);

        $query_String = '';
        if (strpos($request_uri, '?') != false) {
            list($request_uri, $query_String) = explode('?', $request_uri);
        }

        static::setUrl($request_uri ?: '/');
        static::setQueryString($query_String);
    }

    /**
     * @return mixed
     */
    public static function getUrl()
    {
        return static::$url;
    }

    /**
     * @param  mixed  $url
     */
    public static function setUrl($url)
    {
        static::$url = $url;
    }

    /**
     * @return mixed
     */
    public static function getBaseUrl()
    {
        return static::$base_url;
    }

    /**
     * @param  mixed  $base_url
     */
    public static function setBaseUrl($base_url)
    {
        static::$base_url = $base_url;
    }

    /**
     * @return mixed
     */
    public static function getFullUrl()
    {
        return static::$full_url;
    }

    /**
     * @param  mixed  $full_url
     */
    public static function setFullUrl($full_url)
    {
        static::$full_url = $full_url;
    }

    /**
     * @return mixed
     */
    public static function getQueryString()
    {
        return static::$query_string;
    }

    /**
     * @param  mixed  $query_string
     */
    public static function setQueryString($query_string)
    {
        static::$query_string = $query_string;
    }

    /**
     * @return string
     */
    public static function method()
    {
        return Server::get('REQUEST_METHOD');
    }

    /**
     * @param $key
     *
     * @return string
     */
    public static function get($key)
    {
        return static::value($key, $_GET);
    }

    public static function value($key, $type = null)
    {
        $type = isset($type) ? $type : $_REQUEST;

        return static::has($type, $key) ? $type[$key] : null;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public static function has($input, $key)
    {
        return array_key_exists($key, $input);
    }

    /**
     * @param $key
     *
     * @return string
     */
    public static function post($key)
    {
        return static::value($key, $_POST);
    }

    public static function set($key, $value)
    {
        $_REQUEST[$key] = $value;
        $_GET[$key] = $value;
        $_POST[$key] = $value;

        return $value;
    }

    /**
     * @return string
     */
    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    /**
     * @return string
     */
    public static function all()
    {
        return $_REQUEST;
    }
}