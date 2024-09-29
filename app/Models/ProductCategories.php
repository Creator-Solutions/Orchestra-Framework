<?php

namespace app\Models;

use Orchestra\Sonata\Queryable;

/**
 * ------------
 * ProductCategories Class
 * ------------
 * 
 * Handles creating a model relationship
 * between a database table and model class
 */
class ProductCategories extends Queryable
{
   /**
    * @var string
    */
   protected static $table = 'product_categories'; // Define the table for this model

   protected $props = [
      'id',
      'category_name'
   ];

   public function drinks()
   {
      return $this->hasMany(Product::class, 'category');
   }
}
