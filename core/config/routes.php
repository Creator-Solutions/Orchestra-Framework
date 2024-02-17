<?php


include_once(dirname(__DIR__) . '/Controllers/AuthController.php');

use Orchestra\Routing\Router;

class RouteCollection
{

   /**
    * @var Router
    */
   private Router $router;

   /**
    * @var array
    */
   private array $routes;

   public function __construct()
   {
      $this->router = new Router();

      /**
       * Created Routes Here
       */

      $this->router->add('/auth', ['_controller' => AuthController::class, '_callback' => 'login']);
   }

   public function getRouteCollection(): array
   {
      $this->routes = $this->router->getAll();


      return $this->routes;
   }
}
