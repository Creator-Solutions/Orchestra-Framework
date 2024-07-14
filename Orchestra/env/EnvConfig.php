<?php

namespace Orchestra\env;

use Orchestra\io\FileHandler;

/**
 * ---------------------
 * EnvConfig class
 * ---------------------
 * 
 * Main class that handles
 * reading from .env files.
 * 
 * @author creator-solutions/owen
 */
class EnvConfig
{
   private FileHandler $handler;
   private $rootDir;

   public function __construct()
   {
      $this->handler = new FileHandler();
      $this->rootDir = $this->handler->getProjectRoot();
   }

   /**
    * --------------
    * ENV Parse
    * ---------------
    *
    * Parses and reads the 
    * .env file at the project
    * root level
    * @return array
    */
   public function parse(): array
   {
      $env = [];
      $envFiles = [
         'production' => '.env.production',
         'local'      => '.env.local',
      ];

      // Check for both HTTP and HTTPS environments
      $isSecure = isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1);
      $envFile = $isSecure ? $envFiles['production'] : $envFiles['local'];

      // Construct full file paths
      $filePath = $this->rootDir . DIRECTORY_SEPARATOR . $envFile;

      // Attempt to open the environment file
      $file = @fopen($filePath, 'r');
      if ($file) {
         while (($line = fgets($file)) !== false) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '=') !== false) {
               list($key, $value) = explode('=', $line, 2);
               $env[$key] = $value;
            }
         }
         fclose($file);
      } else {
         // If the preferred file isn't found, fallback to '.env'
         $filePath = $this->rootDir . DIRECTORY_SEPARATOR . '.env';
         $file = @fopen($filePath, 'r');
         if ($file) {
            while (($line = fgets($file)) !== false) {
               $line = trim($line);
               if (!empty($line) && strpos($line, '=') !== false) {
                  list($key, $value) = explode('=', $line, 2);
                  $env[$key] = $value;
               }
            }
            fclose($file);
         } else {
            throw new \Exception("Unable to open any environment file: $this->rootDir");
         }
      }

      return $env;
   }

   /**
    * --------
    * getenv func
    * --------
    *
    * Retrieves a specific key
    * from the .env file for specific
    * use. 
    *
    * Automatically parses the .env file
    * for easier use.
    *
    * @param [type] $key
    * @return string
    */
   public function getenv($key): string
   {
      $env = $this->parse();
      return $env[$key];
   }
}
