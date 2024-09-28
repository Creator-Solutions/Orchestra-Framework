<?php

namespace Orchestra\cli\handlers;

use Orchestra\env\EnvConfig;
use Orchestra\io\FileHandler;

class StartupHandler
{

   public static function serveApp()
   {
      // Log: Starting up the server process
      echo "[INFO] Starting the server initialization...\n";

      // Load .env file
      try {
         $env = new EnvConfig();
         echo "[INFO] Environment variables loaded successfully.\n";
      } catch (\Exception $e) {
         echo "[ERROR] Failed to load environment variables: " . $e->getMessage() . "\n";
         exit(1); // Exit if the environment variables can't be loaded
      }

      // Get the port from the environment variables
      $port = $env->getenv("PORT") ?? 8000; // Default to 8000 if not set
      echo "[INFO] Port set to: $port\n";

      // Define the document root directory
      $rootDir = (new FileHandler)->getProjectRoot() . "/public"; // Adjust to your public directory
      echo "[INFO] Document root directory set to: $rootDir\n";

      // Determine local network IP address
      $localIP = getHostByName(getHostName()); // Get the local IP address
      $localAddress = "http://localhost:$port";
      $networkAddress = "http://$localIP:$port";

      $appName = $env->getenv('APP_NAME');

      // Display the formatted startup message
      echo "\nYou can now view $appName in the browser.\n";
      echo "\n  Local:            $localAddress";
      echo "\n  On Your Network:  $networkAddress\n";

      // Start the PHP built-in server
      $command = "php -S localhost:$port -t $rootDir";
      exec($command, $output, $return_var);
      echo "\n";

      // Log: Check if the server was started successfully
      if ($return_var !== 0) {
         echo "[ERROR] Failed to start the server. Please check your PHP installation or port settings.\n";
         exit(1); // Exit on failure
      }
   }
}
