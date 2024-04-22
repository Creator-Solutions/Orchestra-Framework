<?php

namespace core\Controllers;

use Orchestra\http\Request;
use Orchestra\JsonResponse;
use Orchestra\Response;
use Orchestra\routing\Router;
use Orchestra\templates\Template;

use Orchestra\config\OrchidConfig;
use Orchestra\logs\Logger;

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

$orchidConfig = new OrchidConfig();
$config = $orchidConfig->parse();

$logger = new Logger();

Router::get('/page', function (Request $req) {
   $template = new Template();

   $message = "Home Page";

   return $template->view("shared/layout.html", ['message' => $message]);
});


Router::get('/login', function (Request $req) use ($config, $logger) {
   $template = new Template();  

   $message = "Home Page";
   $var = "Variable";
   $logFile = $config['logs'];

   $logger->set_log_directory($logFile);
   $logger->create_log_folder();

   $logger->write($req->get_url(), "unable to identify client");

   return $template->view("shared/layout.html", ['message' => $message, 'var' => $logFile]);
});

Router::get('/view', function (Request $req) {
   $template = new Template();  
   
   $message = "Home Page";
   $var = "Variable";

   return $template->view("shared/layout.html", ['message' => $message, 'var' => $var]);
});

Router::post('/login', function (Request $req) {
   $message = "This is a test request";

   return new JsonResponse(
      array(
         'message' => $message
      ),
      Response::HTTP_OK
   );
});
