<?php

/**
 * Contains the logic for the convenience methods
 * to extract and load yaml files and configurations
 * 
 * @author Creator-Solutions/Owen Burns
 */
namespace Orchestra\Yaml;


class Handler {

   public function parseFile($filePath) {
      // Check if the file exists
      if (!file_exists($filePath)) {
         throw new \InvalidArgumentException("File '$filePath' not found.");
      }
   
      // Read file contents
      $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $data = [];
      $parents = [&$data];
      $currentIndent = 0;
   
      foreach ($lines as $line) {
         // Calculate the indent level
         $indent = strlen($line) - strlen(ltrim($line));
         $line = trim($line);
   
         if ($line === '' || $line[0] === '#') {
               // Skip empty lines and comments
               continue;
         }
   
         // Determine the parent array
         $parent =& $parents[$indent / 2];
   
         // Split key and value
         list($key, $value) = explode(':', $line, 2);
         $key = trim($key);
         $value = trim($value);
   
         // Assign the value to the parent array
         $parent[$key] = $value;
   
         // Update parent array for next iteration
         $parents[$indent / 2 + 1] =& $parent;
      }
   
      return $data;
   }
}