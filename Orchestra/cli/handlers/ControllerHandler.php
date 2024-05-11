<?php

namespace Orchestra\cli\handlers;

use Exception;
use Orchestra\io\FileHandler;

/**
 * ----------------------
 * CLI Database Handler
 * ----------------------
 * 
 * Handles all functionality
 * based on CLI command 
 * linked to DB;
 * 
 * @use Orchestra\storage\DatabaseHelper
 * @use Orchestra\config\OrchidConfig
 * @author founderstud\owen
 * @source namespace Orchestra\cli\handler
 */
class ControllerHandler
{

   private FileHandler $handler;

   public function __construct($arguments)
   {
      $this->handler = new FileHandler();
      $this->configure($arguments);
   }

   public function configure($arguments)
   {
      switch ($arguments[0]) {
         case 'generate':
            $this->generate_controller($arguments[1]);
            break;
      }
   }

   public function generate_controller($controllerName)
   {
      echo "Generating template controller \n";

      try {
         $templateController = $this->handler->getProjectRoot() . "/Orchestra/cli/Controller/Template.php";
         $controllerTempFile = $this->handler->getProjectRoot() . "/core/Controllers/temp.php";

         if (!file_exists($templateController)) {
            echo "Could not find controller template";
            return;
         }

         echo "Generating Controller contents \n";
         if (copy($templateController, $controllerTempFile)) {
            echo "Defining routes \n";
            $controllerFile = $this->handler->getProjectRoot() . "/core/Controllers/$controllerName";
            if (rename($controllerTempFile, $controllerFile)) {
               echo "Action completed successfully \n";
            }
         }
      } catch (Exception $e) {
         echo $e->getMessage();
      }
   }
}
