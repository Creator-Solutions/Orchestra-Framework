<?php

namespace Orchestra\Sonata\Schema;

use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\database\DatabaseHelper;

use PDO;
use Exception;
use Closure;


/**
 * -------------
 * Schema Class
 * -------------
 * 
 * Schema class that handles
 * creating the tables in the 
 * databse
 * 
 * @author creator-solutions/owen
 */
class Schema
{
   protected static ?PDO $pdo = null;

   /**
    * Initialize the PDO connection if not already done.
    */
   protected static function initPDO()
   {
      if (self::$pdo === null) {
         DatabaseHelper::init(); // Initialize the DatabaseHelper connection
         self::$pdo = DatabaseHelper::$conn; // Set the PDO instance
      }
   }

   public static function create($table, Closure $callback): void
   {
      self::initPDO();

      $scheme = new Scheme();
      $callback($scheme);
      $columns = $scheme->getColumns();

      // Generate SQL to create table with columns
      $sql = "CREATE TABLE $table (";
      $columnDefinitions = [];
      $primaryKey = null;

      foreach ($columns as $name => $definition) {
         $columnDefinitions[] = self::getColumnDefinition($name, $definition);

         if (isset($definition['primary']) && $definition['primary']) {
            $primaryKey = $name;
         }
      }

      $sql .= implode(", ", $columnDefinitions);

      if ($primaryKey) {
         $sql .= ", PRIMARY KEY ($primaryKey)";
      }

      $sql .= ");";

      self::$pdo->exec($sql);
   }

   /**
    * Drop a table if it exists.
    * 
    * @param string $table
    * @return bool
    */
   public static function destroyIfExists($table): bool
   {
      self::initPDO();
      try {
         $query = "DROP TABLE IF EXISTS $table";
         $stmt = self::$pdo->prepare($query);
         $stmt->execute();
         return true;
      } catch (Exception $e) {
         // Handle exceptions if necessary
         return false;
      }
   }

   protected static function getColumnDefinition($name, $definition)
   {
      $sql = "$name ";

      switch ($definition['type']) {
         case 'string':
            $sql .= "VARCHAR(" . $definition['length'] . ")";
            break;
         case 'integer':
            $sql .= "INT";
            if (isset($definition['auto_increment']) && $definition['auto_increment']) {
               $sql .= " AUTO_INCREMENT";
            }
            break;
         case 'boolean':
            $sql .= "BOOLEAN";
            break;
         case 'datetime':
            return "$name DATETIME";
         default:
            throw new \Exception("Unknown column type: " . $definition['type']);
      }

      return $sql;
   }
}
