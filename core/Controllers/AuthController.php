<?php

use Orchestra\JsonResponse;
use Orchestra\http\Request;
use Orchestra\storage\DatabaseHelper;
use Orchestra\security\JWT;
use Orchestra\security\Session;

use Orchestra\mailer\PHPMailer;
use Orchestra\mailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Authentication controller
 * Handles user authentication
 * 
 * (c) @author Owen Burns
 * 
 * @author Creator-Solutions Owen Burns
 * @author Founder-Studios Owen Burns
 */
class AuthController extends JsonResponse
{

   /**
    * Request Class Object
    * @var Request
    */
   private Request $req;

   /**
    * @var Session
    */
   private Session $session;

   /**
    * @var array
    */
   private array $response;

   /**
    * @var array
    */
   private array $headers = array(
      'Content-Type' => 'application/json',
   );

   /**
    * @var PDO
    */
   private ?PDO $conn;

   /**
    * Database Seeder class
    * @var DatabaseHelper
    */
   private DatabaseHelper $dbHelper;

   /**
    * @var string
    */
   private string $SQL;

   /**
    * @var PDOStatement
    */
   private PDOStatement $stmt;

   /**
    * response row
    */
   private $row;

   public array $jwtHeaders = array('alg' => 'HS256', 'typ' => 'JWT');

   public function __construct()
   {
      /**
       * Initiates Request class
       */
      $this->req = new Request();

      /**
       * Init session class
       */
      $this->session = new Session();

      /**
       * initializes db seeder          
       */
      $this->dbHelper = new DatabaseHelper();

      /**
       * Assign value to connection
       */
      $this->dbHelper::initMySQL();
      $this->conn = $this->dbHelper::$conn;
   }

   public function login()
   {
      $session = $this->req->get('session');

      if ($this->conn) {
         $this->response = array(
            'Status' => 'connected successfully'
         );
      } else {
         $this->response = array(
            'Status' => 'Not connected successfully'
         );
      }

      return $this->json($this->response[0], 200, $this->headers);
   }
}
