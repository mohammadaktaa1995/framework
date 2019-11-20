<?php


namespace Wolfren\Router;


use Wolfren\Http\Request;

class Route
{
    /**
     * @var array $routes
     */
    private static $routes = [];

    /**
     * @var $middleware
     */
    private static $middleware;

    /**
     * @var $prefix
     */
    private static $prefix;

    private function __construct()
    {

    }

    /**
     * @param $methods
     * @param $uri
     * @param  object|callable  $callback
     */
    private function addRoute($methods, $uri, $callback)
    {
        $uri = trim($uri, '/');
        $uri = rtrim(static::$prefix.'/'.$uri, '/');
        $uri = $uri ?: '/';
        foreach (explode('|', $methods) as $method) {
            static::$routes[] = [
                'uri' => $uri,
                'callback' => $callback,
                'method' => $method,
                'middleware' => static::$middleware,
            ];
        }
    }

    /**
     * @param $uri
     * @param $callback
     */
    public static function get($uri, $callback)
    {
        static::addRoute('GET', $uri, $callback);
    }

    /**
     * @param $uri
     * @param $callback
     */
    public static function post($uri, $callback)
    {
        static::addRoute('POST', $uri, $callback);
    }

    /**
     * @param $uri
     * @param $callback
     */
    public static function any($uri, $callback)
    {
        static::addRoute('GET|POST', $uri, $callback);
    }


    /**
     * @param $uri
     * @param $callback
     */
    public static function patch($uri, $callback)
    {
    }

    /**
     * @param $uri
     * @param $callback
     */
    public static function put($uri, $callback)
    {
    }

    /**
     * @param $uri
     * @param $callback
     */
    public static function delete($uri, $callback)
    {
    }

    public static function prefix($prefix, $callback)
    {
        $parent = static::$prefix;
        static::$prefix .= '/'.trim($prefix, '/');

        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new  \BadFunctionCallException('Please provide valid callback');
        }
        static::$prefix = $parent;
    }

    public static function middleware($middleware, $callback)
    {
        $parent = static::$middleware;
        static::$middleware .= '|'.trim($middleware, '|');

        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new  \BadFunctionCallException('Please provide valid callback');
        }
        static::$middleware = $parent;
    }

    public static function handle()
    {
        $uri = Request::getUrl();

        foreach (static::$routes as $route) {
            $matched = true;
            $route['uri'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['uri']);
            $route['uri'] = '#^'.$route['uri'].'$#';

            if (preg_match($route['uri'], '/'.$uri, $matches)) {
                array_shift($matches);
                $params = array_values($matches);
                foreach ($params as $param) {
                    if (strpos($param, '/')) {
                        $matched = false;
                    }
                }
                if ($route['method'] != Request::method()) {
                    $matched = false;
                    throw new \Exception("Bad Method {$route['method']}.");
                }
                if ($matched) {
                    return static::invoke($route, $params);
                }
            }
        }
        throw new \Exception('Page not Found.');
    }

    public static function invoke($route, $params = [])
    {
        static::excuteMiddleware($route);
        $callback = $route['callback'];
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } elseif (strpos($callback, '@') != false) {
            list($controller, $method) = explode('@', $callback);
            $controller = 'App\Controllers\\'.$controller;

            if (class_exists($controller)) {
                $object = new $controller;
                if (method_exists($object, $method)) {
                    return call_user_func_array([$object, $method], $params);
                } else {
                    throw  new \BadFunctionCallException("The method $method is not exist in $controller");
                }
            } else {
                throw new \ReflectionException("Controller $controller is not found.");
            }

            return call_user_func_array($callback, $params);
        } else {
            throw new \InvalidArgumentException('Please provide valid callback function.');
        }

    }

    public static function excuteMiddleware($route)
    {
        foreach (explode('|', $route['middleware']) as $middleware) {
            if ($middleware != '') {
                $middleware = 'App\Middlewares\\'.ucfirst($middleware);
                if (class_exists($middleware)) {
                    $object = new $middleware;

                    call_user_func_array([$object, 'handle'], []);
                } else {
                    throw new \ReflectionException("Class $middleware is not found.");
                }
            }
        }
    }
}