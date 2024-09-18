<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface
{

   public function build(): void
   {
      Schema::create('intersection', function (Scheme $table) {
         $table->id();
         $table->string('intersection_name');
         $table->integer('intersection_count');
         $table->timestamps();
      });
   }
};
