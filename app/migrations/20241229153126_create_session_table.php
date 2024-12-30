<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface {

   public function build(): void
   {
      Schema::create('sessions', function (Scheme $table) {
         $table->id();
         $table->string("session_id", 100);
         $table->integer("user_id");
         $table->string("ip_addresss", 45);
         $table->text("user_agent");
         $table->text("payload");
         $table->datetime("last_activity");

         $table->timestamps();
      });
   }

   public function destroy()
   {
      Schema::destroyIfExists('');
   }
};
