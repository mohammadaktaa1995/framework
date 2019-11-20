<?php

namespace Wolfren\Bootstrap;

use Wolfren\Exception\Whoops;
use Wolfren\File\File;
use Wolfren\Http\Request;
use Wolfren\Http\Response;
use Wolfren\Router\Route;
use Wolfren\Session\Session;

class App
{
    private function __construct()
    {
    }

    public static function run()
    {
        //Register Erro Handler
        Whoops::handle();

        //Start Session
        Session::start();

        //Handle Request
        Request::handle();
        //Require routes files
        File::require_dir('routes');
        //Handle Route paths
        $data = Route::handle();

        Response::output($data);

    }
}