<?php

namespace Orchestra\cli\handlers;

use Exception;
use DateTime;
use Orchestra\io\FileHandler;

/**
 * ------------------
 * Migration class
 * ------------------
 * 
 * Handles migrating objects to
 * the database
 * 
 * @author creator-solutions/owen
 */
class MigrationHandler
{

   private FileHandler $handler;
   private $migrationName;

   public function __construct($migrationName)
   {
      $this->handler = new FileHandler();
      $this->migrationName = $migrationName;
      $this->generateMigration();
   }

   protected function getTimestamp(): string
   {
      return (new DateTime())->format('YmdHis');
   }

   public function generateMigration()
   {
      try {
         $migrationTemplate = $this->handler->getProjectRoot() . "/Orchestra/cli/Templates/Migration.php";
         $timestamp = $this->getTimestamp();
         $filename = sprintf('%s_%s.php', $timestamp, $this->migrationName);
         $migrationFile = $this->handler->getProjectRoot() . "/app/Migrations/$filename.php";

         if (copy($migrationTemplate, $migrationFile)) {
            echo "\n";
            echo "migration created successfully.............$this->migrationName";
            echo "\n";
         }
      } catch (Exception $e) {
         echo $e;
      }
   }

   public static function migrate()
   {
      $migrationFiles = glob('app/Migrations/*.php');

      foreach ($migrationFiles as $file) {
         $migration = include $file;

         // Ensure that the included file returns an object implementing the MigrationInterface
         if ($migration instanceof \Orchestra\interfaces\MigrationInterface) {
            if (method_exists($migration, 'build')) {
               try {
                  $migration->build();
                  $filename = pathinfo($file, PATHINFO_FILENAME);
                  echo "Migrated: $filename\n";
               } catch (\PDOException $e) {
                  if (strpos($e->getMessage(), 'Base table or view already exists') !== false) {
                     echo "Table already exists: " . $e->getMessage() . "\n";
                     // Continue with the next migration file
                     continue;
                  }
                  echo "Failed to migrate: $file. Error: " . $e->getMessage() . "\n";
               } catch (Exception $e) {
                  echo "Failed to migrate: $file. Error: " . $e->getMessage() . "\n";
               }
            } else {
               echo "No build method found in migration: $file\n";
            }
         } else {
            echo "Invalid migration file: $file. Must return an instance of MigrationInterface.\n";
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
