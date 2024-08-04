<?php

namespace Orchestra\database;

use Orchestra\io\FileHandler;

/**
 * ----------------------
 * Database Services
 * ----------------------
 * 
 * Contains custom config for database 
 * connections and usage.
 * 
 * Config file for database type will always be 
 * present, the Exists condition is to only ensure
 * the path is relevant to the file when deployed.
 * 
 * @author founder-studios/Owen
 */
class Service {

   private $configPath;

   private FileHandler $fileHandler;

   public function __construct()
   {
      $this->fileHandler = new FileHandler();
   }

   /**
    * Retrieves JSON config file that handles database 
    * configurations.
    *
    * Do not make changes to this file as it is only a 
    * convenience method to retrieve values dynamically from
    * the config file
    */
   public function parseConfig(): array {
      $this->configPath = $this->fileHandler->getProjectRoot() . "/orchid-config.json";

      if (!file_exists($this->configPath)){
         throw new \Exception("Config File %s not found", $this->configPath);
      }

      $rawConfig = file_get_contents($this->configPath);
      if (isset($rawConfig)) {
         $config = json_decode($rawConfig, true);

         return $config;
      }

      return [];
   }
}