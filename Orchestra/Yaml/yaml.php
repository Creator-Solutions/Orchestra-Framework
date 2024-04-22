<?php

use Orchestra\Yaml\Parser;

class Yaml {

   public static function parseYaml($path){
      $config = new Orchestra\Yaml\Handler();

      return $config;
   }
}