<?php

namespace app\Models;

use Orchestra\Sonata\Queryable;

/**
 * ------------
 * User Class
 * ------------
 * 
 * Handles creating a model relationship
 * between a database table and model class.
 */
class User extends Queryable
{

   /**
    * @var string
    */
   protected static $table = 'user'; // This must be static and correctly defined

   protected $props = [
      'full_name',
      'email',
      'password'
   ];

   public static function test()
   {
      echo 'Table: ' . static::$table; // Print the table name
   }
}
