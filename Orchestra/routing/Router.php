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
        // Check if middleware exists
        if (isset(Route::$middlewares[$middleware])) {
            // Creates an array of the endpoints associated with the middleware
            $middlewares = Route::$middlewares[$middleware];

            // Flag to check if any middleware endpoint matches the requested URI
            $endpointMatched = false;

            // Loop through the endpoints assigned to the current middleware
            foreach ($middlewares as $middlewareEndpoint) {
                // Check if the current middleware endpoint matches the requested URI
                if ($middlewareEndpoint['endpoint'] === $uri) {
                    // Set flag to true as at least one middleware endpoint matches
                    $endpointMatched = true;

                    // Check for the required header
                    $requiredHeader = $middlewareEndpoint['header'] ?? null;
                    if ($requiredHeader && empty($request->getHeader($requiredHeader))) {
                        return json_encode([
                            "status" => false,
                            "message" => "Missing header"
                        ]);
                    }

                    // Execute the callback function associated with the requested URI
                    $callback = self::$routes[$method][$uri];
                    $response = call_user_func($callback, $request);

                    // Check the type of response
                    if ($response instanceof JsonResponse) {
                        // Send JSON response
                        $response->send();
                    } elseif (is_string($response)) {
                        // Send string response
                        echo $response;
                    }

                    // Exit the loop as we found a matching endpoint
                    break;
                }
            }

            // If none of the middleware endpoints match, return a 404 response
            if (!$endpointMatched) {
                echo "404 Not Found";
            }
        }
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
