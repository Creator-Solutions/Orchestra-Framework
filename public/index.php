<?php

use Orchestra\http\Request;
use Orchestra\http\UrlMatcher;

use Orchestra\routing\Router;
use Orchestra\cli\command\CLI;
use Orchestra\env\EnvConfig;
use Orchestra\logs\Logger;


require_once dirname(__DIR__) . '/autoload.php';


/**
 * -------------------------
 * API File
 * -------------------------
 * 
 * Do not remove this import statement
 * -> COULD HAVE DIRE CONSEQUENCES
 * 
 */
include_once dirname(__DIR__) . '/Orchestra/routing/api.php';

/**
 * ----------------------
 * Controller Imports
 * ----------------------
 * 
 * Import controllers from this part of the file
 */
include_once dirname(__DIR__) . '/app/Controllers/IndexController.php';



/**
 * --------------------
 * Main entry point
 * --------------------
 * 
 * This is the main entry file for a project
 * All requests are caught in this file, the request
 * is then broken into pieces and the respective 
 * endpoint is called linked to the middleware caught by the 
 * URL
 * 
 * Do not make changes to logic below as the logic was carefully
 * placed in order to maximize functionality and quality
 */

$env = new EnvConfig();
Logger::set_log_directory(env('LOG_DIR'));
Logger::create_log_folder();

date_default_timezone_set(env('TIMEZONE'));

if (php_sapi_name() === 'cli') {
   // Remove the script name from the arguments
   array_shift($argv);

   // Parse the command and its arguments
   $command = isset($argv[0]) ? $argv[0] : null;
   $arguments = array_slice($argv, 1);

   $cli = new CLI($command, $arguments);
   $cli->execute();  // Use execute instead of configure
} else {
   // Similar initialization for web requests
   $urlMatcher = new UrlMatcher();

   $requestUri = $_SERVER['REQUEST_URI'];
   $requestMethod = $_SERVER['REQUEST_METHOD'];

   $uri = parse_url($requestUri, PHP_URL_PATH);

   // REMOVE THE SUBDIRECTORY if it exists
   $basePath = '/ci-cloud';  // update this if you ever change subdirectory name
   if (strpos($uri, $basePath) === 0) {
      $uri = substr($uri, strlen($basePath));
   }

   $uri = trim($uri, '/');

   // Optional: normalize to start with `api/`
   if (strpos($uri, 'api/') !== 0) {
      $uri = 'api/' . $uri;
   }

   // Extract the path and middleware
   $urlParts = explode('/', $uri);
   $middleware = count($urlParts) > 2 ? $urlParts[1] : 'default';
   $endpoint = implode('/', array_slice($urlParts, count($urlParts) > 1 ? 2 : 0));

   $response = (string) Router::handle($requestMethod, $middleware, $endpoint, new Request);
   echo $response;
}