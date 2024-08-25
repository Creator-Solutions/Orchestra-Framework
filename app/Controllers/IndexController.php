<?php

namespace App\Controllers;

use app\Models\Test;
use Orchestra\http\Request;
use Orchestra\JsonResponse;
use Orchestra\Response;
use Orchestra\routing\Router;

use Orchestra\io\FileHandler;

use Orchestra\env\EnvConfig;
use Orchestra\templates\Template;
use Orchestra\cache\CacheFactory;

use App\Models\User;
use Exception;

/**
 * ------------------------
 * Controller File Example
 * ------------------------
 * 
 * This is an example file that a controller would look like.
 * No longer requires classes in order to run API's. API's
 * can now be defined by using the Router class as providing
 * the endpoint with a callback
 */

/**
 * -----------------
 * Example Post Request
 * -----------------
 * 
 * Endpoints are registered to the specific callback defined within the post method.
 * Middlewares dictate the callback that has to be executed if the middleware and endpoint
 * match, without having to provide the specific controller that has to be linked to the 
 * middleware value.
 * 
 * This solution allows for less setup work to be done and easier code to maintain as the only methods are already provided,
 * you just provide the logic
 * 
 */

Router::post('/test', function (Request $req) {
   $val = $req->get('test') ?? "";

   return new JsonResponse(
      [
         'message' => 'success',
         'status' => false
      ],
      Response::HTTP_OK
   );
});

Router::get('/', function () {
   return (new Template())->view('welcome');
});
