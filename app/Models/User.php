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

   protected $props = [];
}
