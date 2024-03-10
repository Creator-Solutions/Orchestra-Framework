<?php

namespace Orchestra\routing;

use \InvalidArgumentException;

/**
 * --------------------------------------------
 * Route Provider
 * --------------------------------------------
 * 
 * Here is where all middleware functionality stays.
 * Assigning middlewares will be handled by internal
 * class functions using setter functions and getters
 * for retrieval purposes when loading middleware 
 * services.
 */

class Route
{
   protected static $middlewares = [];
   protected static $middlewareMap = [];
   protected static $alias;

   public static function middleware(string $alias): self
   {
      if (!is_string($alias)) {
         throw new InvalidArgumentException('Alias must be of type string');
      }

      // Set the alias property if it's not already set
      if (!isset(self::$alias)) {
         self::$alias = $alias;
      }

      // Initialize middleware array if it doesn't exist for the current alias
      if (!isset(self::$middlewares[self::$alias])) {
         self::$middlewares[self::$alias] = [];
      }

      return new self();
   }


   public function get(string $endpoint = '/')
   {
      // Ensure the current middleware alias is set
      if (!isset(self::$alias)) {
         throw new InvalidArgumentException('Middleware alias is not set');
      }

      // Initialize middleware array if it doesn't exist for the current alias
      if (!isset(self::$middlewares[self::$alias])) {
         self::$middlewares[self::$alias] = [];
      }

      // Append the endpoint to the array associated with the current middleware alias
      self::$middlewares[self::$alias][] = [
         'endpoint' => $endpoint
      ];

      // Here you would typically define your route handling logic
      // For demonstration, I'm just returning the endpoint and middleware
      return [
         'endpoint' => $endpoint,
         'middlewares' => self::$middlewares[self::$alias], // Return middlewares for current alias
      ];
   }

   /**
    * Get all endpoints linked to a specific middleware key
    *
    * @param string $middlewareKey The middleware key
    * @return array The array of endpoints linked to the middleware key
    */
   public static function getEndpointsForMiddleware(string $middlewareKey): array
   {
      // Check if the middleware key exists in the $middlewares array
      if (isset(self::$middlewares[$middlewareKey])) {
         $endpoints = [];
         foreach (self::$middlewares[$middlewareKey] as $endpointData) {
            // Check if the endpoint key exists in the current endpoint data
            if (isset($endpointData['endpoint'])) {
               $endpoints[] = $endpointData['endpoint'];
            }
         }
         return $endpoints;
      } else {
         // Return an empty array if the middleware key is not found
         return [];
      }
   }


   public static function getAllMiddlewares(): array
   {
      return self::$middlewares;
   }


   /**
    * use function will link a specific middleware point 
    * provided to the respective controller
    */
   public static function use(string $middleware, $controller)
   {
      if (!\is_string($middleware) || empty($middleware)) {
         throw new InvalidArgumentException('"%s": Middleware must be of type string');
      }

      self::$middlewareMap[$middleware] = $controller;
   }

   /**
    * Get the controller associated with a middleware.
    */
   public static function getController(string $middleware): ?string
   {
      return self::$middlewareMap[$middleware] ?? null;
   }
}
