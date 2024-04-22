<?php

namespace Orchestra\Mailer;

/**
 * Basic (for-now) mailing system for the framework.
 * 
 * Makes use of the default mail() function provided by PHP
 * This is basically a utility function that allows you 
 * to incorporate different ways of sending mails without having to install
 * external libraries
 * 
 * @author Creator-Solutions/Owen Burns
 */
class FrameworkMailer
{

   private $username;
   private $password;

   /**
    * Default constructor will require parameters
    * that only have to be set once in a Controller
    * File and can be used in multiple Route endpoints
    *
    *
    * @return None
    */
   public function __construct()
   {

   }
}
