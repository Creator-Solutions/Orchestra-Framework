<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface
{

   public function build(): void
   {
      Schema::create('products', function (Scheme $table) {
         $table->id();
         $table->string('product_name');
         $table->string('SKU');
         $table->integer('price');
         $table->timestamps();
      });
   }

   public function destroy()
   {
      Schema::destroyIfExists('');
   }
};
