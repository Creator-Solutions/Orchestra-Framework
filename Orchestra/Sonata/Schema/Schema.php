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
      $columns = $scheme->getColumns()['columns'];
      $foreignKeys = $scheme->getColumns()['foreign_keys'];

      // Generate SQL to create table with columns
      $sql = "CREATE TABLE $table (";
      $columnDefinitions = [];
      $primaryKey = null;

      foreach ($columns as $name => $definition) {
         // Ensure each column has a valid definition
         if (!isset($definition['type'])) {
            throw new \Exception("Unknown column type for '$name'");
         }
         $columnDefinitions[] = self::getColumnDefinition($name, $definition);

         if (isset($definition['primary']) && $definition['primary']) {
            $primaryKey = $name;
         }
      }

      $sql .= implode(", ", $columnDefinitions);

      if ($primaryKey) {
         $sql .= ", PRIMARY KEY ($primaryKey)";
      }

      // Add foreign key constraints
      foreach ($foreignKeys as $foreignKey) {
         if ($foreignKey['references'] && $foreignKey['on']) {
            $sql .= ", FOREIGN KEY ({$foreignKey['column']}) REFERENCES {$foreignKey['on']}({$foreignKey['references']})";
         }
      }

      $sql .= ");";

      self::$pdo->exec($sql);
   }

   public static function destroyIfExists($table): bool
   {
      self::initPDO();
      try {
         $query = "DROP TABLE IF EXISTS $table";
         $stmt = self::$pdo->prepare($query);
         $stmt->execute();
         return true;
      } catch (Exception $e) {
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
