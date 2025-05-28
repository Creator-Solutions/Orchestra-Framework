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
         $templateController = $this->handler->getProjectRoot() . "/Orchestra/cli/Templates/Controller.php";
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
      $indexFile = $this->handler->getProjectRoot() . "/public/index.php";

      // Read the contents of index.php
      $indexContent = file_get_contents($indexFile);

      // Define the import statement for the new controller
      $importStatement = "include_once dirname(__DIR__) . '/app/Controllers/$controllerName.php';\n";

      // Check if the import statement already exists to avoid duplicates
      if (strpos($indexContent, $importStatement) !== false) {
         echo "Controller already imported.\n";
         return;
      }

      // Define the placeholder comment to insert new imports after
      $placeholder = "include_once dirname(__DIR__) . '/app/Controllers/IndexController.php';";

      // Locate the position of the placeholder
      $position = strpos($indexContent, $placeholder);

      if ($position !== false) {
         // Insert the new import statement directly below the placeholder
         $position += strlen($placeholder) + 1; // Move past the placeholder and add a newline
         $updatedContent = substr($indexContent, 0, $position) . $importStatement . substr($indexContent, $position);

         // Write the updated content back to index.php
         file_put_contents($indexFile, $updatedContent);
         echo "Controller import added successfully.\n";
      } else {
         echo "Placeholder for Controller Imports not found.\n";
      }
   }
}
