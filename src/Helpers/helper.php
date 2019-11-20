<?php

if (! function_exists('view')) {
    function view($view, $data)
    {
        return \PhpFramework\View\View::make($view, $data);
    }
}

if (! function_exists('request')) {
    function request($key)
    {
        return \PhpFramework\Http\Request::value($key);
    }
}

if (! function_exists('redirect')) {
    function redirect($path)
    {
        return \PhpFramework\Url\Url::redirect($path);
    }
}

if (! function_exists('previous')) {
    function previous()
    {
        return \PhpFramework\Url\Url::previous();
    }
}

if (! function_exists('url')) {
    function url($path)
    {
        return \PhpFramework\Url\Url::url($path);
    }
}

if (! function_exists('asset')) {
    function asset($path)
    {
        return \PhpFramework\Url\Url::url($path);
    }
}

if (! function_exists('session')) {
    function session($key)
    {
        return \PhpFramework\Session\Session::get($key);
    }
}

if (! function_exists('flash')) {
    function flash($key)
    {
        return \PhpFramework\Session\Session::flash($key);
    }
}

if (! function_exists('links')) {
    function links($current_page, $pages)
    {
        return \PhpFramework\Database\Database::links($current_page, $pages);
    }
}

if (! function_exists('auth')) {
    function auth($table)
    {
        $auth = \PhpFramework\Session\Session::get($table) ?: \PhpFramework\Cookie\Cookie::get($table);

        return \PhpFramework\Database\Database::table($table)->where('id', '=', $auth)->first();
    }
}

if (! function_exists('dd')) {
    function dd($data)
    {
        echo "<pre>";
        if (! is_string($data)) {
            print_r($data);
        } else {
            echo $data;
        }
        echo "</pre>";
        die();
    }
}