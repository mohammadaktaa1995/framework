<?php


namespace PhpFramework\Url;


use PhpFramework\Http\Request;
use PhpFramework\Http\Server;

class Url
{
    private function __construct()
    {

    }

    public static function url($url)
    {
        return Request::getBaseUrl().trim($url, '/');
    }

    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    public static function redirect($url)
    {
        header('location: '.$url);
        exit(0);
    }
}