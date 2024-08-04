<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface
{

   public function build(): void
   {
      Schema::create('parents', function (Scheme $table) {
         $table->id();
         $table->string('full_name', 50);
         $table->integer('age', 50);
         $table->timestamps();
      });
   }

   public function destroy()
   {
      Schema::destroyIfExists('parents');
   }
};
