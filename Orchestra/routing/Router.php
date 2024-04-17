<?php

namespace Orchestra\routing;

use Orchestra\templates\Template;
use Orchestra\JsonResponse;
use Orchestra\Response;

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
            $response = call_user_func($callback, $request);

            // Check the type of response
            if ($response instanceof JsonResponse) {
                // Send JSON response
                $response->send();
            } elseif (is_string($response)) {
                // Send string response
                echo $response;
            } else {
                // Handle other types of responses (e.g., templates)
                // Implement logic as needed
            }
        } else {
            // Handle route not found
            echo "404 Not Found";
        }
    }

    public static function view($template, $context){
        $templateEngine = new Template($template);
        $templateEngine->render($context);
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
