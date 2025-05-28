<?php

namespace app\Models;

use Orchestra\Sonata\Queryable;

/**
 * ------------
 * Session Class
 * ------------
 * 
 * Handles creating a model relationship
 * between a database table and model class
 */
class Sessions extends Queryable
{

   /**
    * @var string
    */
   protected static $table = 'sessions';

   protected $props = [
      'session_id',
      'user_id',
      'ip_address',
      'user_agent',
      'payload',
      'last_activity',
   ];

   public function get()
   {
      // Map the properties you want to be part of the JSON response
      return $this->__get(['id', 'session_id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity']);
   }
}
