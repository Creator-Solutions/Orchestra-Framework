<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface
{

   public function build(): void
   {
      Schema::create('user', function (Scheme $table) {
         $table->id();
         $table->string('username');
         $table->integer('age');
         $table->timestamps();
      });
   }

   public function destroy()
   {
      Schema::destroyIfExists('');
   }
};
