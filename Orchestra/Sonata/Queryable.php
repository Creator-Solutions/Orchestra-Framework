<?php

namespace Orchestra\Sonata;

use Orchestra\database\DatabaseHelper;
use PDO;
use Exception;

/**
 * ----------------
 * Queryable Trait
 * ----------------
 * 
 * Queryable trait that enables
 * querying directly from the 
 * model.
 */
abstract class Queryable
{
   protected static $table;
   protected static $conn;
   protected $attributes = [];

   public function __construct(array $attributes = [])
   {
      $this->initConnection();
      $this->attributes = $attributes;
   }

   private function initConnection()
   {
      if (!self::$conn) {
         // Assume MySQL for the example; change to initPG() for PostgreSQL
         DatabaseHelper::init();
         self::$conn = DatabaseHelper::$conn;
      }
   }

   public static function create(array $data)
   {
      $instance = new static($data); // Create a new instance with provided data
      $table = static::getTable();
      $columns = implode(', ', array_keys($data));
      $placeholders = implode(', ', array_fill(0, count($data), '?'));

      $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
      $statement = self::$conn->prepare($sql);
      $statement->execute(array_values($data));

      $instance->attributes['id'] = self::$conn->lastInsertId();
      return $instance;
   }

   public static function find($id)
   {
      $instance = new static(); // Create a new instance of the subclass
      $table = static::getTable();
      $sql = "SELECT * FROM $table WHERE id = ?";
      $statement = self::$conn->prepare($sql);
      $statement->execute([$id]);
      $result = $statement->fetch(PDO::FETCH_ASSOC);

      if ($result) {
         return new static($result); // Return a new instance with the found data
      }

      return null;
   }

   public function save()
   {
      $table = static::getTable();
      $id = $this->attributes['id'] ?? null;

      if ($id) {
         // Update existing record
         $setClause = [];
         $values = [];
         foreach ($this->attributes as $column => $value) {
            $setClause[] = "$column = ?";
            $values[] = $value;
         }
         $values[] = $id; // Add the ID to the values array for the WHERE clause
         $setClause = implode(', ', $setClause);

         $sql = "UPDATE $table SET $setClause WHERE id = ?";
         $statement = self::$conn->prepare($sql);
         $statement->execute($values);
      } else {
         // Insert new record
         static::create($this->attributes);
      }
   }

   public static function delete($id)
   {
      $instance = new static(); // Ensure the connection is initialized
      $table = static::getTable();

      $sql = "DELETE FROM $table WHERE id = ?";
      $statement = self::$conn->prepare($sql);
      $statement->execute([$id]);

      return $statement->rowCount();
   }

   public function __get($key)
   {
      return $this->attributes[$key] ?? null;
   }

   public function __set($key, $value)
   {
      $this->attributes[$key] = $value;
   }

   protected static function getTable()
   {
      return static::$table;
   }

   protected static function executeQuery($query, $params)
   {
      if (!self::$conn) {
         $instance = new static(); // Ensure the connection is initialized
      }

      $statement = self::$conn->prepare($query);
      $statement->execute($params);

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }
}
