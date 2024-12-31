<?php

namespace Orchestra\forte;

use Orchestra\forte\ForteException;
use Exception;
use Orchestra\database\RecordBuilder;
use Orchestra\logs\LogTypes;
use Orchestra\logs\Logger;

class Forte
{

   public static function getUserRoles()
   {
      try {
         $headerRoles = $_SERVER['HTTP_Roles'];

         return $headerRoles;
      } catch (Exception $e) {
         throw new ForteException("Headers not provided", 0, $e);
      }
   }

   public static function getAuthUser()
   {
      try {
         $builder = new RecordBuilder();

         $authorizationHeader = null;

         // Check if the Authorization header exists in the request
         if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
         } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            // For some server configurations, the Authorization header might be under 'REDIRECT_HTTP_AUTHORIZATION'
            $authorizationHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
         }

         if ($authorizationHeader) {
            // Extract the token from the Authorization header
            $token = str_replace('Bearer ', '', $authorizationHeader);  // Assuming 'Bearer' token

            $userId = $builder
               ->from('sessions')
               ->where('session_id', '=', $token)
               ->selectFirst(['user_id']);

            $user = $builder
               ->from('user')
               ->where('id', '=', $userId['user_id'])
               ->selectFirst(['*']);


            if (isset($user)) {
               return $user;
            } else {
               return ["message" => "User doesn't exist", "status" => true];
            }
         } else {
            echo "Authorization header not found.";
         }

      } catch (Exception $ex) {
         Logger::write($ex->getMessage(), LogTypes::EXCEPTION);
      }
   }

   public static function hash_argon(string $password): string
   {
      return password_hash($password, PASSWORD_ARGON2ID);
   }

   public static function hash_bcrypt(string $password): string
   {
      return password_hash($password, PASSWORD_BCRYPT);
   }

   public static function hash_argon2i(string $password): string
   {
      return password_hash($password, PASSWORD_ARGON2I);
   }
}
