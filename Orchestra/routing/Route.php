<?php

namespace Orchestra\routing;

use Orchestra\forte\Forte;
use \InvalidArgumentException;

/**
 * --------------------------------------------
 * Route Provider
 * --------------------------------------------
 * 
 * Simplified route handling class with middleware support.
 */
class Route
{
   protected static $middlewares = [];
   protected static $middlewareMap = [];
   protected static $alias;

   /**
    * Define middleware alias for a route.
    *
    * @param string $alias
    * @return self
    * @throws InvalidArgumentException
    */
   public static function middleware(string $alias): self
   {
      if (!isset(self::$middlewares[$alias])) {
         self::$middlewares[$alias] = [];
      }

      self::$alias = $alias;
      return new self();
   }

   /**
    * Define a route for GET requests.
    *
    * @param string $endpoint
    * @return array
    * @throws InvalidArgumentException
    */
   public function get(string $endpoint = '/'): array
   {
      return $this->addEndpoint($endpoint);
   }

   /**
    * Define a protected route requiring specific headers.
    *
    * @param string $endpoint
    * @return array
    * @throws InvalidArgumentException
    */
   public function getProtected(string $endpoint): array
   {
      $parts = explode(':', $endpoint);
      return $this->addEndpoint($parts[0], $parts[1] ?? null);
   }

   /**
    * Add an endpoint to the middleware.
    *
    * @param string $endpoint
    * @param string|null $header
    * @return array
    * @throws InvalidArgumentException
    */
   protected function addEndpoint(string $endpoint, ?string $header = null): array
   {
      if (!isset(self::$alias)) {
         throw new InvalidArgumentException('Middleware alias is not set');
      }

      $endpointData = ['endpoint' => $endpoint];
      if ($header) {
         $endpointData['header'] = $header;
      }

      self::$middlewares[self::$alias][] = $endpointData;

      return [
         'endpoint' => $endpoint,
         'header' => $header,
         'middlewares' => self::$middlewares[self::$alias]
      ];
   }

   /**
    * Get all endpoints associated with a specific middleware.
    *
    * @param string $middlewareKey
    * @return array
    */
   public static function getEndpointsForMiddleware(string $middlewareKey): array
   {
      return array_column(self::$middlewares[$middlewareKey] ?? [], 'endpoint');
   }

   /**
    * Get all defined middlewares.
    *
    * @return array
    */
   public static function getAllMiddlewares(): array
   {
      return self::$middlewares;
   }

   /**
    * Map a middleware to a specific controller.
    *
    * @param string $middleware
    * @param string $controller
    * @throws InvalidArgumentException
    */
   public static function use(string $middleware, string $controller)
   {
      if (empty($middleware)) {
         throw new InvalidArgumentException('Middleware must be a non-empty string');
      }
      self::$middlewareMap[$middleware] = $controller;
   }

   /**
    * Get the controller associated with a middleware.
    *
    * @param string $middleware
    * @return string|null
    */
   public static function getController(string $middleware): ?string
   {
      return self::$middlewareMap[$middleware] ?? null;
   }
}