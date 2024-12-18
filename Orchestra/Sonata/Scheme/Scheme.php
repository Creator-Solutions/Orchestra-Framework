<?php

namespace Orchestra\Sonata\Scheme;

/**
 * --------------
 * Scheme Class
 * --------------
 * 
 * Scheme class that contains
 * methods to create table columns
 */
class Scheme
{
   protected $columns = [];
   protected $foreignKeys = [];
   protected $currentForeignKey = null;

   public function id($column_name = 'id')
   {
      $this->columns[$column_name] = [
         'type' => 'integer',
         'primary' => true,
         'auto_increment' => true,
      ];
      return $this;
   }

   public function string($column_name, $length = 255)
   {
      $this->columns[$column_name] = [
         'type' => 'string',
         'length' => $length,
      ];
      return $this;
   }

   public function integer($column_name)
   {
      $this->columns[$column_name] = [
         'type' => 'integer',
      ];
      return $this;
   }

   public function boolean($column_name)
   {
      $this->columns[$column_name] = [
         'type' => 'boolean',
      ];
      return $this;
   }

   public function text($column_name)
   {
      $this->columns[$column_name] = [
         'type' => 'text'
      ];

      return $this;
   }

   public function datetime($column_name)
   {
      $this->columns[$column_name] = [
         'type' => 'datetime',
      ];

      return $this;
   }

   public function timestamps()
   {
      $this->columns['created_at'] = [
         'type' => 'datetime',
      ];
      $this->columns['updated_at'] = [
         'type' => 'datetime',
      ];
      return $this;
   }

   public function foreign($column_name)
   {
      if (!isset($this->columns[$column_name])) {
         $this->columns[$column_name] = [
            'type' => 'integer', // Foreign keys are typically integers
         ];
      }

      $this->currentForeignKey = [
         'column' => $column_name,
         'references' => null,
         'on' => null,
      ];
      return $this;
   }

   public function references($references)
   {
      if ($this->currentForeignKey) {
         $this->currentForeignKey['references'] = $references;
      }
      return $this;
   }

   public function on($table)
   {
      if ($this->currentForeignKey) {
         $this->currentForeignKey['on'] = $table;
         $this->foreignKeys[] = $this->currentForeignKey;
         $this->currentForeignKey = null;
      }
      return $this;
   }

   public function getColumns()
   {
      return [
         'columns' => $this->columns,
         'foreign_keys' => $this->foreignKeys,
      ];
   }
}
