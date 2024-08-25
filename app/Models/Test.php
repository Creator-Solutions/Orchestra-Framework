<?php

namespace app\Models;

use Orchestra\Sonata\Queryable;

/**
 * ------------
 * Test Class
 * ------------
 * 
 * Handles creating a model relationship
 * between a database table and model class
 */
class Test extends Queryable
{

   /**
    * @var string
    */
   protected static $table = '';

   protected $props = [];
}
