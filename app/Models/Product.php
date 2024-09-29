<?php

namespace app\Models;

use Orchestra\Sonata\Queryable;

/**
 * ------------
 * Product Class
 * ------------
 * 
 * Handles creating a model relationship
 * between a database table and model class
 */
class Product extends Queryable
{

   /**
    * @var string
    */
   protected static $table = 'products';

   protected $props = [
      'id',
      'product_name',
      'SKU',
      'price',
      'category',
   ];
}
