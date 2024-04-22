<?php

namespace Orchestra\routing;

use Orchestra\templates\Template;
use Orchestra\JsonResponse;
use Orchestra\Response;

use Orchestra\http\UrlMatcher;
use Orchestra\bandwidth\TokenBucket;
use Orchestra\bandwidth\Rate;
use Orchestra\bandwidth\BlockingConsumer;
use Orchestra\bandwidth\storage\FileStorage;
use Orchestra\bandwidth\storage\SessionStorage;
use Orchestra\io\FileHandler;

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

    protected static function applyRateLimit(string $uri)
    {
        // Initialize the storage, rate, and token bucket
        $storage = new SessionStorage("Founders");
        $rate = new Rate(10, Rate::MINUTE);
        $bucket = new TokenBucket(10, $rate, $storage);
        $bucket->bootstrap(10);

        try{
            // Consume tokens from the bucket
            if (!$bucket->consume(1)) {
                // Rate limit exceeded
                http_response_code(429);
                echo json_encode([
                    'state' => false,
                    'message' => 'Rate limit exceeded'
                ]);
                exit(); // Stop further execution
            }
        }catch (\Exception $e){
            echo $e;
        }
       
    }

    public static function handle(string $method, string $uri, $request)
    {
        // Check if the method exists in the routes array
        if (isset(self::$routes[$method][$uri])) {
            // Apply rate limit
            self::applyRateLimit($uri);

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
