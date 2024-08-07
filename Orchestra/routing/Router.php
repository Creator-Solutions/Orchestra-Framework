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
        self::registerRoute('POST', $path, $callback);
    }

    public static function get(string $path, callable $callback)
    {
        self::registerRoute('GET', $path, $callback);
    }

    public static function put(string $path, callable $callback)
    {
        self::registerRoute('PUT', $path, $callback);
    }

    public static function delete(string $path, callable $callback)
    {
        self::registerRoute('DELETE', $path, $callback);
    }

    protected static function convertPathToRegex(string $path): array
    {
        $params = [];
        $pattern = preg_replace_callback('/{(\w+)}/', function ($matches) use (&$params) {
            $params[] = $matches[1];
            return '(\w+)'; // or '([^/]+)' for more general matching
        }, $path);

        return [
            'pattern' => "#^{$pattern}$#",
            'params' => $params
        ];
    }

    // Register a route with a given method, path, and callback
    protected static function registerRoute(string $method, string $path, callable $callback)
    {
        // Convert the path into a regex pattern and extract parameter names
        $regex = self::convertPathToRegex($path);
        self::$routes[$method][$regex['pattern']] = [
            'callback' => $callback,
            'params' => $regex['params']
        ];
    }

    protected static function applyRateLimit(string $uri)
    {
        // Initialize the storage, rate, and token bucket
        $storage = new SessionStorage("Founders");
        $rate = new Rate(10, Rate::MINUTE);
        $bucket = new TokenBucket(10, $rate, $storage);
        $bucket->bootstrap(10);

        try {
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
        } catch (\Exception $e) {
            echo $e;
        }
    }

    public static function handle(string $method, string $middleware, string $uri, $request)
    {
        // Apply middleware and rate limiting (if any)
        self::applyRateLimit($uri);

        // Initialize the routes array for the method if not already done
        if (!isset(self::$routes[$method])) {
            self::$routes[$method] = [];
        }

        // Loop through the registered routes for the given method
        foreach (self::$routes[$method] as $pattern => $route) {
            // Check if the URI matches the route pattern
            if (preg_match($pattern, $uri, $matches)) {
                // Remove the first match (the full match)
                array_shift($matches);

                // Extract parameters from the matches
                $params = [];
                foreach ($route['params'] as $index => $paramName) {
                    $params[$paramName] = $matches[$index] ?? null;
                }

                // Add request and params as arguments to the callback
                $response = call_user_func_array($route['callback'], array_merge([$request], $params));

                // Handle the response (JSON, string, etc.)
                if ($response instanceof JsonResponse) {
                    $response->send();
                } elseif (is_string($response)) {
                    echo $response;
                }

                return; // Stop after the first match
            }
        }

        // If no route matches, return a 404 response
        echo "404 Not Found";
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
