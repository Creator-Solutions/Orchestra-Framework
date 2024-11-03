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

if (php_sapi_name() === 'cli') {
   // Remove the script name from the arguments
   array_shift($argv);

   // Parse the command and its arguments
   $command = isset($argv[0]) ? $argv[0] : null;
   $arguments = array_slice($argv, 1);

   // Initialize Logger and set log directory
   $env = new EnvConfig();
   Logger::set_log_directory($env->getenv('LOG_DIR'));
   Logger::create_log_folder();

   $cli = new CLI($command, $arguments);
   $cli->execute();  // Use execute instead of configure
} else {
   // Similar initialization for web requests
   $urlMatcher = new UrlMatcher();

   $env = new EnvConfig();
   Logger::set_log_directory($env->getenv('LOG_DIR'));
   Logger::create_log_folder();

   $requestUri = $_SERVER['REQUEST_URI'];
   $requestMethod = $_SERVER['REQUEST_METHOD'];

   // Remove the query string if present
   $uri = parse_url($requestUri, PHP_URL_PATH);
   $uri = trim($uri, '/'); // Remove leading and trailing slashes

   // Extract the path and middleware
   $urlParts = explode('/', $uri);
   $middleware = count($urlParts) > 1 ? $urlParts[0] : 'default';
   $endpoint = implode('/', array_slice($urlParts, count($urlParts) > 1 ? 1 : 0));

   $response = Router::handle($requestMethod, $middleware, $endpoint, new Request);
   echo $response;
}
