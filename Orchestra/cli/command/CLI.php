<?php

namespace Orchestra\cli\command;

use Orchestra\cli\handlers\ControllerHandler;


/**
 * --------------------------
 * Command Line Interpretor
 * --------------------------
 * 
 * Main class that handles command line commands.
 * Delegates functions based on command arguments
 * 
 * @author founderstud/owen
 * @source namespace Orchestra\cli\command
 */
class CLI
{

   private $command;
   private $arguments;

   private ControllerHandler $dbHandler;

   public function __construct($command, $arguments)
   {
      $this->command = $command;
      $this->arguments = $arguments;
   }

   public function configure()
   {
      switch ($this->command) {
         case 'controller':
            $this->dbHandler = new ControllerHandler($this->arguments);
            break;
      }
   }
}
