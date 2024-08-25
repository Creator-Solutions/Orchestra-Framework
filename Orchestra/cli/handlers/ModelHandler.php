<?php

namespace Orchestra\cli\handlers;

use Orchestra\io\FileHandler;

/**
 * -------------------
 * ModelHandler Class
 * -------------------
 * 
 * Handles creating the model 
 * class using command lines
 * 
 *  * @author creator-solutions/owen
 */
class ModelHandler
{

   private FileHandler $handler;
   private $modelName;

   public function __construct($modelName)
   {
      $this->handler = new FileHandler();
      $this->modelName = $modelName;
      $this->generateModel();
   }

   public function generateModel()
   {
      try {
         $templateModel = $this->handler->getProjectRoot() . "/Orchestra/cli/Templates/Model.php";
         $modelFile = $this->handler->getProjectRoot() . "/app/Models/$this->modelName.php";
         $modelFolder = $this->handler->getProjectRoot() . "/app/Models";

         if (!file_exists($templateModel)) {
            echo "Could not find model template";
            return;
         }

         if (!file_exists($modelFolder)) {
            mkdir($modelFolder, 0755, true); // Ensure the directory is created with proper permissions
         }

         if (copy($templateModel, $modelFile)) {
            echo "Model created successfully\n";

            // Read the file content
            $content = file_get_contents($modelFile);

            // Split content into lines
            $lines = explode("\n", $content);

            // Replace 'Model' with the actual model name, except in the namespace line
            foreach ($lines as &$line) {
               if (strpos($line, 'namespace') !== 0) {
                  $line = str_replace('Model', $this->modelName, $line);
               }
            }

            // Join the lines back together
            $updatedContent = implode("\n", $lines);

            // Write the updated content back to the file
            file_put_contents($modelFile, $updatedContent);

            echo "Model class name updated successfully\n";
         }
      } catch (\Exception $e) {
         echo "An error occurred: " . $e->getMessage();
      }
   }
}
