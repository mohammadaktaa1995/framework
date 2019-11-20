<?php

if (! function_exists('view')) {
    function view($view, $data)
    {
        return \Wolfren\View\View::make($view, $data);
    }
}

if (! function_exists('request')) {
    function request($key)
    {
        return \Wolfren\Http\Request::value($key);
    }
}

if (! function_exists('redirect')) {
    function redirect($path)
    {
        return \Wolfren\Url\Url::redirect($path);
    }
}

if (! function_exists('previous')) {
    function previous()
    {
        return \Wolfren\Url\Url::previous();
    }
}

if (! function_exists('url')) {
    function url($path)
    {
        return \Wolfren\Url\Url::url($path);
    }
}

if (! function_exists('asset')) {
    function asset($path)
    {
        return \Wolfren\Url\Url::url($path);
    }
}

if (! function_exists('session')) {
    function session($key)
    {
        return \Wolfren\Session\Session::get($key);
    }
}

if (! function_exists('flash')) {
    function flash($key)
    {
        return \Wolfren\Session\Session::flash($key);
    }
}

if (! function_exists('links')) {
    function links($current_page, $pages)
    {
        return \Wolfren\Database\Database::links($current_page, $pages);
    }
}

if (! function_exists('auth')) {
    function auth($table)
    {
        $auth = \Wolfren\Session\Session::get($table) ?: \Wolfren\Cookie\Cookie::get($table);

        return \Wolfren\Database\Database::table($table)->where('id', '=', $auth)->first();
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