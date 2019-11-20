<?php

namespace PhpFramework\Bootstrap;

use PhpFramework\Exception\Whoops;
use PhpFramework\File\File;
use PhpFramework\Http\Request;
use PhpFramework\Http\Response;
use PhpFramework\Router\Route;
use PhpFramework\Session\Session;

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