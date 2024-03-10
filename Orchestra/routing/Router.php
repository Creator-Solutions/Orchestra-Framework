<?php

namespace Orchestra\routing;

/**
 * ------------------------------
 * Router Class
 * ------------------------------
 * 
 * Each route defined using the internal methods from
 * this class will be stored by the request method 
 * as well as the /middleware/endpoint path. Once
 * a request has been made to the endpoint a callback
 * will be executed based on the parameters
 * 
 */
class Router
{

    protected static $routes = [];

    public static function post(string $path, callable $callback)
    {
        self::$routes['POST'][$path] = $callback;
    }

    public static function get(string $path, callable $callback)
    {
        self::$routes['GET'][$path] = $callback;
    }

    public static function handle(string $method, string $uri, $request)
    {
        // Check if the method exists in the routes array
        if (isset(self::$routes[$method][$uri])) {
            // If yes, execute the callback function
            $callback = self::$routes[$method][$uri];
            return call_user_func($callback, $request);
        } else {
            // Handle route not found
            echo "404 Not Found";
        }
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
