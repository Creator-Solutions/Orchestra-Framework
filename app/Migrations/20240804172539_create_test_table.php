<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface
{
   public function build(): void
   {
      Schema::create('students', function (Scheme $table) {
         $table->id();
         $table->string('name', 50);
         $table->string('email', 50);
         $table->integer('age', 50);
      });
   }
};
