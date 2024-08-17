<?php

use Orchestra\http\Request;
use Orchestra\http\UrlMatcher;

use Orchestra\routing\Router;
use Orchestra\cli\command\CLI;

use Orchestra\config\OrchidConfig;
use Orchestra\logs\Logger;


require_once 'autoload.php';

/**
 * -------------------------
 * API File
 * -------------------------
 * 
 * Do not remove this import statement
 * -> COULD HAVE DIRE CONSEQUENCES
 * 
 */
include_once(__DIR__ . '/Orchestra/routing/api.php');

/**
 * ----------------------
 * Controller Imports
 * ----------------------
 * 
 * Import controllers from this part of the file
 */
include_once(__DIR__ . '/app/Controllers/IndexController.php');



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
   $orchidConfig = new OrchidConfig();
   $config = $orchidConfig->parse();
   Logger::set_log_directory($config['logs']);
   Logger::create_log_folder();

   $cli = new CLI($command, $arguments);
   $cli->execute();  // Use execute instead of configure
} else {
   // Similar initialization for web requests
   $urlMatcher = new UrlMatcher();
   $orchidConfig = new OrchidConfig();
   $config = $orchidConfig->parse();
   // Set and create log directory
   Logger::set_log_directory($config['logs']);
   Logger::create_log_folder();
   $requestUri = $_SERVER['REQUEST_URI'];
   $requestMethod = $_SERVER['REQUEST_METHOD'];
   
   $uri = parse_url($requestUri, PHP_URL_PATH);
   $urlParts = explode('/', trim($uri, '/'));

   $middleware = $urlParts[0] ?? 'default';
   $endpoint = '/' . ($urlParts[1] ?? '');

   $response = Router::handle($requestMethod, $middleware, $endpoint, new Request);
   echo $response;
}
