<?php

namespace Orchestra\cli\command;

use Exception;

class Command
{

   public function __construct($command)
   {
      switch ($command) {
         case 'migrate':
            $this->migrate();
            break;
      }
   }

   public static function migrate()
   {
      $migrationFiles = glob('app/Migrations/*.php');

      foreach ($migrationFiles as $file) {
         require_once $file;

         $className = basename($file, '.php');
         if (class_exists($className)) {
            $migration = new $className();
            if (method_exists($migration, 'build')) {
               try {
                  $migration->build();
                  echo "Migrated: $className\n";
               } catch (Exception $e) {
                  echo "Failed to migrate: $className. Error: " . $e->getMessage() . "\n";
               }
            }
         }
      }
   }

   public static function rollback()
   {
      $migrationFiles = glob('app/Migrations/*.php');

      foreach ($migrationFiles as $file) {
         require_once $file;

         $className = basename($file, '.php');
         if (class_exists($className)) {
            $migration = new $className();
            if (method_exists($migration, 'destroy')) {
               try {
                  $migration->destroy();
                  echo "Rolled back: $className\n";
               } catch (Exception $e) {
                  echo "Failed to roll back: $className. Error: " . $e->getMessage() . "\n";
               }
            }
         }
      }
   }
}
