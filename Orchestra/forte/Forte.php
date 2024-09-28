<?php

namespace Orchestra\forte;

use Orchestra\http\Request;

/**
 * ------------------
 * Main Forte Class
 * ------------------
 * 
 * Handles various auth
 * utilities.
 * 
 * @author creator-solutions/owen
 */
class Forte
{

   private $jwtHeaders = array('alg' => 'HS256', 'typ' => 'JWT');

   public function getAuthUser(Request $req)
   {
      $authHeader = $req->getHeader('Authorization') ?? "";
   }

   public function createToken() {}
}
