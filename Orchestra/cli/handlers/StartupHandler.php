<?php

namespace Orchestra\cli\handlers;

use Orchestra\env\EnvConfig;

class StartupHandler
{

   public static function serveApp()
   {
      // Load .env file
      $env = new EnvConfig();

      // Get the port from the environment variables
      $port = $env->getenv("PORT") ?? 8000; // Default to 8000 if not set

      // Define the document root directory
      $rootDir = __DIR__ . '/../../../public'; // Adjust to your public directory

      // Start the PHP built-in server
      $command = "php -S localhost:$port -t $rootDir";
      echo "Starting PHP server on port $port...\n";
      exec($command);
   }
}
