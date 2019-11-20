<?php

namespace Wolfren\Exception;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Whoops{
    private function __construct()
    {

    }

    public static function handle()
    {
        $whoops = new Run;
        $whoops->prependHandler(new PrettyPageHandler);
        $whoops->register();
    }
}
