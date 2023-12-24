<?php

namespace Orchestra\io;

class FileHandler{

    /**
     * @var string
     */
    private string $rootProjectFolder;

   public function getProjectRoot(): string {
      $current_dir = __DIR__;
  
      $depthToRoot = 0;
  
      // Specify the number of levels to go up
      $levelsToRoot = 2;
  
      // Keep moving up in the directory structure until you reach the desired root depth
      while ($depthToRoot < $levelsToRoot) {
          // Move up one directory level
          $current_dir = dirname($current_dir);
          $depthToRoot++;
      }
  
      // Set the root project folder
      $this->rootProjectFolder = $current_dir;
  
      return $this->rootProjectFolder;
   }
}

$handler = new FileHandler();
echo $handler->getProjectRoot();