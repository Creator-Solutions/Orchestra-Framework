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
   protected $recordBuilder;
   protected $table;
   protected static $conn;
   protected static $attributes = [];
   protected static $whereClause = '';
   protected static $data = [];

   // Initialize the database connection statically
   private static function initConnection()
   {
      if (!self::$conn) {
         // Assume MySQL for the example; change to initPG() for PostgreSQL
         DatabaseHelper::init();
         self::$conn = DatabaseHelper::$conn;
      }
   }

   public static function create(array $data)
   {
      self::initConnection(); // Ensure connection is initialized
      self::$attributes = $data;

      $table = static::getTable();
      $columns = implode(', ', array_keys($data));
      $placeholders = implode(', ', array_fill(0, count($data), '?'));

      $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
      $statement = self::$conn->prepare($sql);
      $statement->execute(array_values($data));

      self::$attributes['id'] = self::$conn->lastInsertId();
      return self::$attributes;
   }

   public static function find($id)
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();
      $sql = "SELECT * FROM $table WHERE id = ?";
      $statement = self::$conn->prepare($sql);
      $statement->execute([$id]);
      $result = $statement->fetch(PDO::FETCH_ASSOC);

      if ($result) {
         self::$attributes = $result;
         return $result;
      }

      return null;
   }

   public static function save()
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();
      $id = self::$attributes['id'] ?? null;

      if ($id) {
         // Update existing record
         $setClause = [];
         $values = [];
         foreach (self::$attributes as $column => $value) {
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
         self::create(self::$attributes);
      }
   }

   public static function delete($id)
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();

      $sql = "DELETE FROM $table WHERE id = ?";
      $statement = self::$conn->prepare($sql);
      $statement->execute([$id]);

      return $statement->rowCount();
   }

   public static function all()
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();

      $sql = "SELECT * FROM $table";
      return self::executeQuery($sql, []);
   }

   public static function limit($limit)
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();

      $sql = "SELECT * FROM $table LIMIT ?";
      return self::executeQuery($sql, [$limit]);
   }

   public static function where($column, $operator, $value)
   {
      if (!empty(self::$whereClause)) {
         self::$whereClause .= ' AND ';
      }
      self::$whereClause .= "$column $operator ?";
      self::$data[] = $value;
      return new static(); // Allows chaining calls
   }

   public static function select($columns = ['*'])
   {
      self::initConnection(); // Ensure connection is initialized

      // Ensure $columns is an array
      if (is_string($columns)) {
         $columns = [$columns]; // Convert string to array
      }

      $columns = implode(', ', $columns);  // Now implode will work properly

      $sql = "SELECT $columns FROM " . static::getTable();
      if (!empty(self::$whereClause)) {
         $sql .= " WHERE " . self::$whereClause;
      }

      $statement = self::$conn->prepare($sql);
      $statement->execute(self::$data);

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }

   public static function selectFirst($columns = ['*'])
   {
      self::initConnection(); // Ensure connection is initialized
      $columns = implode(', ', $columns);
      $sql = "SELECT $columns FROM " . static::getTable();
      if (!empty(self::$whereClause)) {
         $sql .= " WHERE " . self::$whereClause;
      }
      $statement = self::$conn->prepare($sql);
      $statement->execute(self::$data);
      return $statement->fetch(PDO::FETCH_ASSOC);
   }

   public static function deleteWhere()
   {
      self::initConnection(); // Ensure connection is initialized
      if (empty(static::$table)) {
         throw new Exception("Table name not specified.");
      }
      if (empty(self::$whereClause)) {
         throw new Exception("WHERE clause not specified for delete operation.");
      }

      $sql = "DELETE FROM " . static::getTable() . " WHERE " . self::$whereClause;
      $statement = self::$conn->prepare($sql);
      $statement->execute(self::$data);
      return $statement->rowCount();
   }

   public static function belongsToMany($related, $pivotTable, $foreignKey, $relatedKey)
   {
      self::initConnection(); // Ensure connection is initialized

      // Get the current model's table name
      $table = static::getTable();

      // Get the related model's table name
      $relatedInstance = new $related();
      $relatedTable = $relatedInstance->getTable();

      // Build the SQL query for the many-to-many relationship
      $sql = "SELECT $relatedTable.* FROM $relatedTable 
            INNER JOIN $pivotTable ON $relatedTable.id = $pivotTable.$relatedKey
            WHERE $pivotTable.$foreignKey = ?";

      $statement = self::$conn->prepare($sql);

      // Assuming the current model's 'id' is set in self::$attributes
      $statement->execute([self::$attributes['id']]);

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }

   public function __get($key)
   {
      return self::$attributes[$key] ?? null;
   }

   public function __set($key, $value)
   {
      self::$attributes[$key] = $value;
   }

   protected static function getTable()
   {
      return static::$table;
   }

   protected static function executeQuery($query, $params)
   {
      self::initConnection(); // Ensure connection is initialized
      $statement = self::$conn->prepare($query);
      $statement->execute($params);

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }
}
