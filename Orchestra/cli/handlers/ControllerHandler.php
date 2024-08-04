<?php

namespace Orchestra\cli\handlers;

use Exception;
use Orchestra\io\FileHandler;

class ControllerHandler
{
   private FileHandler $handler;
   private $controllerName;

   public function __construct($controllerName)
   {
      $this->handler = new FileHandler();
      $this->controllerName = $controllerName;
      $this->generate_controller($this->controllerName);
   }

   public function generate_controller($controllerName)
   {
      echo "Generating controller \n";

      try {
         $templateController = $this->handler->getProjectRoot() . "/Orchestra/cli/Controller/Template.php";
         $controllerFile = $this->handler->getProjectRoot() . "/app/Controllers/$controllerName.php"; // Ensure .php extension

         if (!file_exists($templateController)) {
            echo "Could not find controller template";
            return;
         }

         echo "Generating Controller contents \n";
         if (copy($templateController, $controllerFile)) {
            echo "Defining routes \n";
            $this->updateIndexFile($controllerName); // Update index.php
            echo "Action completed successfully \n";
         }
      } catch (Exception $e) {
         echo $e->getMessage();
      }
   }

   private function updateIndexFile($controllerName)
   {
      $indexFile = $this->handler->getProjectRoot() . "/index.php";
      $importStatement = "include_once(__DIR__ . '/app/Controllers/$controllerName.php');\n";

      // Read the contents of index.php
      $indexContent = file_get_contents($indexFile);

      // Check if the import statement already exists
      if (strpos($indexContent, $importStatement) === false) {
         // Find the position to insert the new import statement
         $position = strrpos($indexContent, 'include_once(__DIR__ . \'/app/Controllers/IndexController.php\');');

         if ($position !== false) {
            // Insert the new import statement after the existing ones
            $position += strlen('include_once(__DIR__ . \'/app/Controllers/IndexController.php\');') + 1;
            $indexContent = substr($indexContent, 0, $position) . $importStatement . substr($indexContent, $position);

            // Write the updated content back to index.php
            file_put_contents($indexFile, $indexContent);
         } else {
            // If no existing import statement found, just append it
            file_put_contents($indexFile, $importStatement, FILE_APPEND);
         }
      }
   }
}
