<?php

namespace Orchestra\routing;

use Orchestra\JsonResponse;
use Orchestra\bandwidth\TokenBucket;
use Orchestra\bandwidth\Rate;
use Orchestra\bandwidth\storage\SessionStorage;

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
        self::applyRateLimit($uri);

        $middlewares = Route::getEndpointsForMiddleware($middleware);
        foreach ($middlewares as $mw) {
            $controller = Route::getController($mw);
            if ($controller) {
                $controllerInstance = new $controller();
                $controllerInstance->handle($request);
            }
        }

        foreach (self::$routes[$method] as $pattern => $route) {
            if (preg_match($pattern, '/' . $uri, $matches)) {
                array_shift($matches);

                $params = [];
                foreach ($route['params'] as $index => $paramName) {
                    $params[$paramName] = $matches[$index] ?? null;
                }

                $response = call_user_func_array($route['callback'], array_merge([$request], $params));

                if ($response instanceof JsonResponse) {
                    $response->send();
                } elseif (is_string($response)) {
                    echo $response;
                }

                return;
            }
        }

        echo "404 Not Found\n";
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
