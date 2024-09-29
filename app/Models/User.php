<?php

namespace app\Models;

use Orchestra\Sonata\Queryable;

/**
 * ------------
 * User Class
 * ------------
 * 
 * Handles creating a model relationship
 * between a database table and model class
 */
class User extends Queryable
{

   /**
    * @var string
    */
   protected static $table = 'user';

   protected $props = ['id', 'username', 'age', 'created_at', 'updated_at'];

   public function get()
   {
      // Map the properties you want to be part of the JSON response
      return [
         'id' => $this->id,
         'username' => $this->username,
         'age' => $this->age,
         'created_at' => $this->created_at,
         'updated_at' => $this->updated_at,
      ];
   }
}
