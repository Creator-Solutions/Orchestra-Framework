<?php

namespace Orchestra\logs;

use Exception;
use Orchestra\io\FileHandler;

/**
 * -------------------
 * Logger Class
 * -------------------
 * 
 * Handles logging information and errors to the log
 * file provided in the orchid-config file
 */
class Logger extends FileHandler{
   private $logDIR;
   private $logFile;

   public function set_log_directory($path){
      $this->logDIR = $path;
   }

   public function create_log_folder(){
      $log_folder = $this->getProjectRoot() . $this->logDIR;

      if (!is_dir($log_folder)){
         if (!mkdir($log_folder, 0777, true)){
            throw new Exception("Could not create log directory");
         }
      }

      $this->logFile = $log_folder . 'error.log';
      if (!file_exists($this->logFile)){
         if (!touch($this->logFile)){
            throw new Exception("Could not create log file");
         }
      }
   }

   public function write($endpoint, $message){
          // Get the current date and time
    $timestamp = date('Y-m-d H:i:s');

    $callingScript = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'];

    $formattedLogMessage = "[$timestamp]: endpoint: $endpoint, Controller: $callingScript, message: $message" . PHP_EOL;

    $result = file_put_contents($this->logFile, $formattedLogMessage, FILE_APPEND);

    // Check if the write operation was successful
    if ($result === false) {
        // Failed to write to log file
        return false;
    }

    // Successfully wrote to log file
    return true;
   }
}