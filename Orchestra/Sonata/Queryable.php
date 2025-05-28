<?php

namespace Orchestra\Sonata;

use PDO;
use Exception;

use Orchestra\database\DB;
use Orchestra\logs\Logger;
use Orchestra\logs\LogTypes;

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
   public $attributes = [];
   protected static $whereClauses = []; // Store where clauses
   protected static $whereParams = [];
   private static $orderByClause = '';
   protected static $data = [];


   // Initialize the database connection statically
   private static function initConnection()
   {
      if (!self::$conn) {
         DB::init();
         self::$conn = DB::$conn;
      }
   }

   // Magic method to handle dynamic method calls (for instantiated models)
   public function __call($method, $arguments)
   {
      if (method_exists($this, $method)) {
         return call_user_func_array([$this, $method], $arguments);
      }

      throw new Exception("Method {$method} does not exist on this model.");
   }


   // Magic method to handle static method calls
   public static function __callStatic($method, $arguments)
   {
      $instance = new static(); // Create an instance of the called class

      if (method_exists($instance, $method)) {
         return call_user_func_array([$instance, $method], $arguments);
      }

      throw new Exception("Method {$method} does not exist on this model.");
   }

   public static function create(array $data)
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();
      $columns = implode(', ', array_keys($data));
      $placeholders = implode(', ', array_fill(0, count($data), '?'));

      $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
      $statement = self::$conn->prepare($sql);
      $statement->execute(array_values($data));

      $data['id'] = self::$conn->lastInsertId(); // Assign the new ID to the data array
      return (object) $data; // Return the newly created attributes
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
         $instance = new static(); // Create an instance of the model
         $instance->attributes = $result; // Set instance attributes to the result
         return $instance;
      }

      return null;
   }

   public function save()
   {
      self::initConnection(); // Ensure connection is initialized

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
         $values[] = $id; // Add ID to the values array for the WHERE clause
         $setClause = implode(', ', $setClause);

         $sql = "UPDATE $table SET $setClause WHERE id = ?";
         $statement = self::$conn->prepare($sql);

         // Execute the statement and return the result as true or false
         return $statement->execute($values);
      } else {
         // Insert new record
         $newData = self::create($this->attributes);

         if ($newData) {
            $this->attributes = $newData; // Update the attributes with the new data
            return true; // Return true on successful insertion
         } else {
            return false; // Return false if insertion fails
         }
      }
   }

   public static function delete()
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();

      if (empty(self::$whereClauses)) {
         throw new Exception("Delete operation requires at least one WHERE condition.");
      }

      $whereClause = implode(' AND ', self::$whereClauses);
      $sql = "DELETE FROM $table WHERE $whereClause";

      $statement = self::$conn->prepare($sql);
      $statement->execute(self::$whereParams);

      // Reset where clauses and parameters after execution to prevent unintended reuse
      self::$whereClauses = [];
      self::$whereParams = [];

      return $statement->rowCount();
   }

   public static function deleteFromId($column, $id)
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();

      if (empty(self::$whereClauses)) {
         throw new Exception("Delete operation requires at least one WHERE condition.");
      }

      $sql = "DELETE FROM $table WHERE $column = $id";

      $statement = self::$conn->prepare($sql);
      $statement->execute();

      // Reset where clauses and parameters after execution to prevent unintended reuse
      self::$whereClauses = [];
      self::$whereParams = [];

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
      self::$whereClauses[] = "$column $operator :$column";
      self::$whereParams[":$column"] = $value;

      return new static; // Return the current instance for method chaining
   }

   public static function and($column, $operator, $value)
   {
      // Append the condition with AND
      self::$whereClauses[] = "AND $column $operator :$column";
      self::$whereParams[":$column"] = $value;

      return new static; // Return the current instance for method chaining
   }

   public static function orderBy($column, $direction = 'ASC')
   {
      $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
      self::$orderByClause = "ORDER BY $column $direction";
      return new static; // Return the current instance for method chaining
   }

   public static function max($column)
   {
      self::$whereClauses[] = "$column = (SELECT MAX($column) FROM " . static::$table . ")";
      return new static; // Return the current instance for method chaining
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

      // Check if there are any where clauses and add them to the SQL query
      if (!empty(self::$whereClauses)) {
         // Join the where clauses with AND
         $sql .= " WHERE " . implode(' AND ', self::$whereClauses);
      }

      Logger::write("Executing Query: $sql", LogTypes::INFORMATION);

      $statement = self::$conn->prepare($sql);

      // Execute the statement with the bound parameters from where clauses
      $statement->execute(self::$whereParams);

      $result = $statement->fetchAll(PDO::FETCH_ASSOC);

      // Reset state after execution
      self::clearState();
      self::clearWhereClauses();

      return $result;
   }


   public static function selectFirst($columns = ['*'])
   {
      self::initConnection();

      $table = static::getTable();

      // Handle special case for selecting all columns
      if ($columns === '*') {
         $columns = ['*'];
      }

      if (is_string($columns)) {
         $columns = [$columns]; // Convert string to array
      }

      $columnList = implode(', ', $columns);
      $sql = "SELECT $columnList FROM $table";

      // Add where clauses if they exist
      if (!empty(self::$whereClauses)) {
         $sql .= ' WHERE ' . implode(' AND ', self::$whereClauses);
      }

      $statement = self::$conn->prepare($sql);

      // Bind parameters
      foreach (self::$whereParams as $key => $value) {
         $statement->bindValue($key, $value);
      }

      Logger::write("Executing query: $sql with params: " . json_encode(self::$whereParams), LogTypes::INFORMATION);

      try {
         $statement->execute();

         // Set the fetch mode to return instances of the current model class
         $statement->setFetchMode(PDO::FETCH_CLASS, static::class);

         // Fetch the first result
         $result = $statement->fetch();

         if (!$result) {
            return null; // Return null if no record is found
         }

         self::$whereClauses = [];
         self::$whereParams = [];

         return (object) $result; // Return the first result as an instance of the model
      } catch (\PDOException $e) {
         // Handle exception (logging or rethrowing)
         throw new Exception("Database query error: " . $e->getMessage());
      } finally {
         // Clear where clauses for the next call
         self::clearWhereClauses();
      }
   }

   public static function count($column = '*')
   {
      self::initConnection(); // Ensure connection is initialized
      $table = static::getTable();

      // Validate column name to prevent SQL injection
      if ($column !== '*' && !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
         throw new Exception("Invalid column name for count operation");
      }

      $sql = "SELECT COUNT($column) as count FROM $table";

      // Add WHERE clauses if they exist
      if (!empty(self::$whereClauses)) {
         $sql .= ' WHERE ' . implode(' AND ', self::$whereClauses);
      }

      $statement = self::$conn->prepare($sql);

      // Bind parameters
      foreach (self::$whereParams as $key => $value) {
         $statement->bindValue($key, $value);
      }

      Logger::write("Executing count query: $sql with params: " . json_encode(self::$whereParams), LogTypes::INFORMATION);

      $statement->execute();
      $result = $statement->fetch(PDO::FETCH_ASSOC);

      // Reset where clauses for the next call
      self::clearWhereClauses();

      return (int) $result['count'];
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

      $sql = "DELETE FROM " . static::getTable() . " WHERE " . self::$whereClauses;
      $statement = self::$conn->prepare($sql);
      $statement->execute(self::$data);
      return $statement->rowCount();
   }

   public function belongsToMany($related, $pivotTable, $foreignKey, $relatedKey)
   {
      self::initConnection();

      // Get the current model's table name
      $table = static::getTable();

      // Get the related model's table name
      $relatedInstance = new $related();
      $relatedTable = $relatedInstance->getTable();

      // Build the SQL query for the many-to-many relationship
      $sql = "SELECT $relatedTable.* FROM $relatedTable 
               INNER JOIN $pivotTable ON $relatedTable.id = $pivotTable.$relatedKey
               WHERE $pivotTable.$foreignKey = ?";

      Logger::write($sql, LogTypes::INFORMATION);

      $statement = self::$conn->prepare($sql);

      // Assuming the current model's 'id' is set in self::$attributes
      $statement->execute([$this->attributes['id']]);

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }

   // Example hasMany relationship
   public function hasMany($related, $foreignKey)
   {
      self::initConnection();

      $relatedInstance = new $related();
      $relatedTable = $relatedInstance->getTable();

      // Ensure 'id' exists in attributes
      if (!isset(self::$attributes['id'])) {
         throw new Exception('ID is not set in attributes.');
      }

      $sql = "SELECT * FROM $relatedTable WHERE $foreignKey = ?";
      $statement = self::$conn->prepare($sql);
      $statement->execute([self::$attributes['id']]);

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }

   public function hasOne($related, $foreignKey)
   {
      self::initConnection(); // Ensure connection is initialized

      // Get the related model's table name
      $relatedInstance = new $related();
      $relatedTable = $relatedInstance->getTable();

      // Build the SQL query for the one-to-one relationship
      $sql = "SELECT * FROM $relatedTable WHERE $foreignKey = ? LIMIT 1";

      $statement = self::$conn->prepare($sql);
      $statement->execute([$this->attributes['id']]);

      return $statement->fetch(PDO::FETCH_ASSOC);
   }

   public function belongsTo($related, $foreignKey)
   {
      self::initConnection(); // Ensure connection is initialized

      // Get the related model's table name
      $relatedInstance = new $related();
      $relatedTable = $relatedInstance->getTable();

      // Build the SQL query for the belongs-to relationship
      $sql = "SELECT * FROM $relatedTable WHERE id = ?";

      $statement = self::$conn->prepare($sql);
      $statement->execute([self::$attributes[$foreignKey]]);

      return $statement->fetch(PDO::FETCH_ASSOC);
   }

   public function __get($keys)
   {
      // If the $keys parameter is an array
      if (is_array($keys)) {
         $result = [];
         foreach ($keys as $key) {
            // Return the value for each key or null if the key does not exist
            $result[$key] = $this->attributes[$key] ?? null;
         }
         return $result;
      }

      // If the $keys parameter is a single key (not an array)
      return $this->attributes[$keys] ?? null;
   }

   public function __set($key, $value)
   {
      $this->attributes[$key] = $value;
   }

   protected static function getTable()
   {
      return static::$table; // Access the static table from the child class
   }

   protected static function executeQuery($query, $params)
   {
      self::initConnection();
      $statement = self::$conn->prepare($query);
      $statement->execute($params);

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }

   // Clear the query state
   private static function clearState()
   {
      self::$whereClauses = [];
      self::$data = [];
   }

   protected static function clearWhereClauses()
   {
      self::$whereClauses = [];
      self::$whereParams = [];
   }


   public static function setTable($tableName)
   {
      static::$table = $tableName;
   }
}
