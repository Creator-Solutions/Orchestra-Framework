<?php

namespace app\Models;

use Orchestra\Sonata\Queryable;

/**
 * ------------
 * Model Class
 * ------------
 * 
 * Handles creating a model relationship
 * between a database table and model class
 */
class Model extends Queryable
{

   /**
    * @var string
    */
   protected static $table = '';

   protected $props = [];

   public function get()
   {
      // Map the properties you want to be part of the JSON response
      return [
         'id' => $this->id,
      ];
   }
}
