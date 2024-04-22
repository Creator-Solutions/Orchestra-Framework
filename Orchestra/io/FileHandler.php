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

   /**
    * Retrieves the config.yaml file
    * to automatically load any controller
    * or template configuration into the system
    */
   public function readConfig($path){
      $configPath = $this->getProjectRoot() . '/core/config/config.yaml';
		
		$config = $this->parseYamlFile($configPath);
		echo $config['path'] . "<br />";
		echo $config['namespace'] . "<br />";

		return $config;
   }

	function parseYamlFile($filePath) {

  }
  
}

$handler = new FileHandler();