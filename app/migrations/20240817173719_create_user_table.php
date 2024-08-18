<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface
{

   public function build(): void
   {
      Schema::create('', function (Scheme $table) {
         $table->id();
         $table->timestamps();
      });
   }

   public function destroy()
   {
      Schema::destroyIfExists('');
   }
};
