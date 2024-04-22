<?php

namespace Orchestra\config\Exception;

use \Exception;
/**
 * Custom Exceptions for the config functionality
 * Conveniennce classes to allow for easier debugging
 * during development
 */
class FileNotFoundException extends Exception{
   public function __construct($message){
      throw new \Exception($message);
   }
}

class ElementNotFoundException extends Exception{
   public function __construct($message){
      throw new \Exception($message);
   }
}

