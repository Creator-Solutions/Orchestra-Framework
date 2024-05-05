
        // Check if the method exists in the routes array
        if (isset(self::$routes[$method][$uri])) {
            // Check if middleware is defined for this route
            if (isset(self::$routes['middleware'][$uri])) {
                $middleware = self::$routes['middleware'][$uri];
                // Execute middleware before the route callback
                foreach ($middleware as $alias) {
                    // Check if the middleware alias has a callback
                    if (isset(Route::getAllMiddlewares()[$alias])) {
                        $middlewareCallback = Route::getAllMiddlewares()[$alias];
                        $middlewareResponse = call_user_func($middlewareCallback, $request);
                        // If middleware returns a response, send it immediately
                        if ($middlewareResponse instanceof JsonResponse) {
                            $middlewareResponse->send();
                            return;
                        }
                    }
                }
            }

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
        } elseif (isset(self::$routes['middleware'][$uri])) {
            // If the URI is not found in routes registered with middleware, return 404
            http_response_code(404);
            echo "404 Not Found";
        } else {
            // Handle route not found
            echo "404 Not Found";
        }