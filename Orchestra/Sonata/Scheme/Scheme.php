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

   public function id($column_name = 'id')
   {
      $this->columns[$column_name] = [
         'type' => 'integer',
         'primary' => true,
         'auto_increment' => true
      ];
      return $this;
   }

   public function string($column_name, $length = 255)
   {
      $this->columns[$column_name] = [
         'type' => 'string',
         'length' => $length
      ];
      return $this;
   }

   public function integer($column_name)
   {
      $this->columns[$column_name] = [
         'type' => 'integer'
      ];
      return $this;
   }

   public function boolean($column_name)
   {
      $this->columns[$column_name] = [
         'type' => 'boolean'
      ];
      return $this;
   }

   public function timestamps()
   {
      $this->columns['created_at'] = [
         'type' => 'datetime'
      ];
      $this->columns['updated_at'] = [
         'type' => 'datetime'
      ];
      return $this;
   }

   public function getColumns()
   {
      return $this->columns;
   }
}
