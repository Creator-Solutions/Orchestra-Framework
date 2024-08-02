<?php

namespace core\Controllers;

use Orchestra\http\Request;
use Orchestra\JsonResponse;
use Orchestra\Response;
use Orchestra\routing\Router;

use Orchestra\storage\RecordBuilder;
use Orchestra\io\FileHandler;

use Orchestra\env\EnvConfig;

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

$builder = new RecordBuilder();
$fileHandler = new FileHandler();
$env = new EnvConfig();

Router::post('/create', function (Request $req) use ($env) {
   $envConfig = $env->parse();
   
   return new JsonResponse(
      [
         'status' => true,
         'host' => $envConfig,
      ],
      Response::HTTP_OK
   );
});
