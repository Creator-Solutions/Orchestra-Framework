<?php

namespace Orchestra\database;

use Orchestra\database\DatabaseHelper;

use \PDO;
use \Exception;

/**
 * Main Class that handles SQL Calls
 * -> Inits Connection Object Directly.
 * 
 * (c) author 
 * 
 * author founderstudios/owenburns
 */
class RecordBuilder extends DatabaseHelper
{
   private $pdo;
   private $table;
   private $data;
   private $whereClause;

   private $topFirstIndex;


   public function __construct(string $table = "")
   {
      $this->pdo = $this->getConnectionConfig();
      $this->table = $table;
      $this->data = [];
      $this->whereClause = '';
      $this->topFirstIndex = false;
   }

   public function getConnectionConfig(): PDO
   {
      try {
         $this->init();
      } catch (Exception $ex) {
         throw new Exception($ex);
      }


      return self::$conn;
   }

   public function from(string $table)
   {
      $this->table = $table;
      return $this;
   }

   public function set($column, $value)
   {
      $this->data[$column] = $value;
      return $this;
   }

   public function where($column, $operator, $value)
   {
      if (!empty($this->whereClause)) {
         $this->whereClause .= ' AND ';
      }
      $this->whereClause .= "$column $operator ?";
      $this->data[] = $value;
      return $this;
   }

   public function or($column, $operator, $value)
   {
      if (!empty($this->whereClause)) {
         $this->whereClause .= " OR ";
      }
      $this->whereClause .= "$column $operator ?";
      $this->data[] = $value;
      return $this;
   }

   public function and($column, $operator, $value)
   {
      if (!empty($this->whereClause)) {
         $this->whereClause .= " AND ";
      }
      $this->whereClause .= "$column $operator ?";
      $this->data[] = $value;
      return $this;
   }

   private function clearData()
   {
      $this->data = [];
      $this->whereClause = '';
   }

   public function insert()
   {
      if (empty($this->table)) {
         throw new Exception("Table name not specified.");
      }

      $columns = implode(', ', array_keys($this->data));
      $placeholders = implode(', ', array_fill(0, count($this->data), '?'));

      $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
      $statement = $this->pdo->prepare($sql);
      $statement->execute(array_values($this->data));

      $this->clearData();

      return $this->pdo->lastInsertId();
   }

   public function update()
   {
      if (empty($this->table)) {
         throw new Exception("Table name not specified.");
      }
      if (empty($this->whereClause)) {
         throw new Exception("WHERE clause not specified for update operation.");
      }

      $setClause = [];
      $values = [];
      foreach ($this->data as $column => $value) {
         // Skip if the value is 0 or null
         if ($value === 0 || $value === null) {
            continue;
         }
         $setClause[] = "$column = ?";
         $values[] = $value;
      }
      $setClause = implode(', ', $setClause);

      $sql = "UPDATE $this->table SET $setClause WHERE $this->whereClause";
      $sql = $this->removeSubstring($sql, ", 0 = ?");
      $statement = $this->pdo->prepare($sql);
      $statement->execute($values);

      $this->clearData();

      return $statement->rowCount();
   }

   public function delete()
   {
      if (empty($this->table)) {
         throw new Exception("Table name not specified.");
      }
      if (empty($this->whereClause)) {
         throw new Exception("WHERE clause not specified for delete operation.");
      }

      $sql = "DELETE FROM $this->table WHERE $this->whereClause";
      $statement = $this->pdo->prepare($sql);
      $statement->execute(array_values($this->data));

      $this->clearData();

      return $statement->rowCount();
   }

   public function select($columns = ['*'])
   {
      if (empty($this->table)) {
         throw new Exception("Table name not specified.");
      }

      $columns = implode(', ', $columns);
      $sql = "SELECT $columns FROM $this->table";
      if (!empty($this->whereClause)) {
         $sql .= " WHERE $this->whereClause";
      }
      $statement = $this->pdo->prepare($sql);
      $statement->execute(array_values($this->data));

      $this->clearData();

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }

   public function selectFirst($columns = ['*'])
   {
      if (empty($this->table)) {
         throw new Exception("Table name not specified.");
      }

      $columns = implode(', ', $columns);
      $sql = "SELECT $columns FROM $this->table";
      if (!empty($this->whereClause)) {
         $sql .= " WHERE $this->whereClause";
      }
      $statement = $this->pdo->prepare($sql);
      $statement->execute(array_values($this->data));

      $this->clearData();

      return $statement->fetch(PDO::FETCH_ASSOC); // Return only the first row
   }

   // Method to execute a stored procedure
   public function callProcedure(string $procedureName, array $parameters = [])
   {
      $placeholders = rtrim(str_repeat('?, ', count($parameters)), ', ');
      $sql = "CALL $procedureName($placeholders)";
      $statement = $this->pdo->prepare($sql);
      $statement->execute($parameters);
      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }

   // Method to execute a stored function
   public function callFunction(string $functionName, array $parameters = [])
   {
      $placeholders = rtrim(str_repeat('?, ', count($parameters)), ', ');
      $sql = "SELECT $functionName($placeholders)";
      $statement = $this->pdo->prepare($sql);
      $statement->execute($parameters);
      return $statement->fetchColumn();
   }

   public function removeSubstring(string $originalString, string $substringToRemove): string
   {
      $startIndex = strpos($originalString, $substringToRemove); // Find the starting index of the substring to remove
      if ($startIndex !== false) { // Check if the substring to remove exists
         $length = strlen($substringToRemove); // Get the length of the substring to remove
         $originalString = substr_replace($originalString, "", $startIndex, $length); // Remove the substring
      }
      return $originalString;
   }
}
