<?php

namespace Orchestra\storage;

use Orchestra\storage\DatabaseHelper;


use \PDO;
use \Exception;
use PDOStatement;

/**
 * Record Builder class that construct SQL Queries
 * -> Initializes DB connection object directly from base class
 * 
 * (c) @author 
 * 
 * @author Creator-Solutions -> Owen Burns
 */
class RecordBuilder
{
   private $pdo;
   private $table;
   private $data;
   private $whereClause;

   public function __construct(PDO $pdo, string $table)
   {
      $this->pdo = $pdo;
      $this->table = $table;
      $this->data = [];
      $this->whereClause = '';
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

   public function insert()
   {
      $columns = implode(', ', array_keys($this->data));
      $placeholders = implode(', ', array_fill(0, count($this->data), '?'));

      $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
      $statement = $this->pdo->prepare($sql);
      $statement->execute(array_values($this->data));

      return $this->pdo->lastInsertId();
   }

   public function select($columns = ['*'])
   {
      $columns = implode(', ', $columns);
      $sql = "SELECT $columns FROM $this->table";
      if (!empty($this->whereClause)) {
         $sql .= " WHERE $this->whereClause";
      }
      $statement = $this->pdo->prepare($sql);
      $statement->execute(array_values($this->data));

      return $statement->fetchAll(PDO::FETCH_ASSOC);
   }
}
