<?php

namespace Orchestra\cli\handlers;

use Exception;
use Orchestra\io\FileHandler;

/**
 * --------------------
 * Template Handler
 * --------------------
 * 
 * Handles creating templates via the CLI
 * 
 * @author creator-solutions/owen
 */
class TemplateHandler
{
   private FileHandler $handler;

   public function __construct($templateName, $subCommand)
   {
      $this->handler = new FileHandler();
      switch ($subCommand) {
         case 'component':
            $this->createTemplate($templateName);
            break;
         case 'template':
            break;
      }
   }

   private function createTemplate($templateName)
   {
      echo "Generating controller \n";

      try {

         $templateComponent = $this->handler->getProjectRoot() . "/Orchestra/cli/Templates/Template.php";
         $path = $this->handler->getProjectRoot() . "/app/resources/views/components";

         //Check if the components folder exists first
         if (!is_dir($path))
            mkdir($path, 0777, true);

         $componentPath = "$path/$templateName.pulse.php";

         echo "Generating Template contents \n";

         if (copy($templateComponent, $componentPath)) {
            echo "\n";
            echo "Action completed successfully \n";
            echo "\n";
         }
      } catch (Exception $e) {
         echo $e->getMessage();
      }
   }
}
