<?php

use Orchestra\http\Request;
use Orchestra\http\UrlMatcher;
use Orchestra\routing\Route;
use Orchestra\routing\Router;
use Orchestra\cli\command\CLI;

if (!defined('PHPNEXUS_VERSION')) {
   require_once 'autoload.php';
}

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
include_once(__DIR__ . '/core/Controllers/IndexController.php');
include_once(__DIR__ . '/core/Controllers/SecondController.php');

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

   print_r($arguments);

   $cli = new CLI($command, $arguments);
   $cli->configure();

} else {
   $urlMatcher = new UrlMatcher();

   $requestUri = $_SERVER['REQUEST_URI'];
   $requestMethod = $_SERVER['REQUEST_METHOD'];

   // Extract middleware and endpoint
   $uri = parse_url($requestUri, PHP_URL_PATH);
   $urlParts = explode('/', $uri);
   $middleware = $urlParts[2];
   $endpoint = $urlMatcher->serializeUrl($urlParts);

   // Get routes and handle request
   $response = Router::handle($requestMethod, $middleware, $endpoint, new Request);
   echo $response;
}
