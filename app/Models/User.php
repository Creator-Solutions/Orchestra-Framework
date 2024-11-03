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

   protected $props = [
      'username',
      'age'
   ];

   public function get()
   {
      // Map the properties you want to be part of the JSON response
      return [
         'id' => $this->attributes['id'] ?? null, // Use $this->attributes instead of direct property access
         'username' => $this->attributes['username'] ?? null,
         'age' => $this->attributes['age'] ?? null,
      ];
   }
}
