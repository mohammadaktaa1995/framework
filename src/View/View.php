<?php


namespace PhpFramework\View;


use Jenssegers\Blade\Blade;
use PhpFramework\File\File;
use PhpFramework\Session\Session;

class View
{

    private function __construct()
    {

    }

    /**
     * @param  string  $view
     * @param  array  $data
     *
     * @return false|string
     * @throws \Exception
     */
    public static function render($view, $data)
    {
        $path = 'views'.File::ds().str_replace(['/', '\\', '.'], File::ds(), $view).'.php';
        if (! File::exist($path)) {
            throw  new \Exception("View $view isn't exist");
        }
        ob_start();
        extract($data);

        include File::path($path);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public static function make($view, $data)
    {
        $blade = new Blade(File::path('views'), File::path('storage/cache'));
        $data = array_merge($data, ['errors' => Session::flash('errors'), 'old' => Session::flash('old')]);
        echo $blade->make($view, $data)->render();
    }

}