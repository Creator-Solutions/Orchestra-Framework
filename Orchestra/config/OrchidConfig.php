<?php

namespace Orchestra\config;

use Orchestra\config\Exception\ElementNotFoundException;
use Orchestra\config\Exception\FileNotFoundException;

use Orchestra\io\FileHandler;


/**
 * ----------------------
 * Orchid Config class
 * ----------------------
 * 
 * Main class that handles reading the orchid-config.json file
 * Can be used to customize the framework's information and 
 * functionality like Database setup and 
 * 
 * @author Creator-Solutions / Owen Burns
 */
class OrchidConfig {

   private $configPath;
   private $config;

   private FileHandler $handler;

   /**
    * Basic constructor
    * Sets the config file path relative to the 
    * root directory
    * 
    * @param None
    * @return None
    */
   public function __construct(){
      $this->handler = new FileHandler();
      $this->configPath = $this->handler->getProjectRoot() . '/orchid-config.json';
   }  

   /**
    * Convenience method to parse the config file
    * that returns an array decoded from the JSON data
    *
    * @param None
    * @return Array
    */
   public function parse(): array{
      if (!file_exists($this->configPath)){
         throw new FileNotFoundException("Config $this->configPath cannot be located", );
      }

      $configData = file_get_contents($this->configPath);
      if (isset($configData)){
         $this->config = json_decode($configData, true);

         return $this->config;
      }

      return [];
   }

   /**
    * Allows you retrieve a specific item from the config file 
    * for instance database type (MySQL, PostgreSQL)
    *
    * @param key Key element in the config array
    * @return mixed
    */
   public function get_config_key($key): mixed{
      if (array_key_exists($key, $this->config)){
         return $this->config[$key];
      }else {
         throw new ElementNotFoundException("Element $key could not be found in the array");
      }
   }
}