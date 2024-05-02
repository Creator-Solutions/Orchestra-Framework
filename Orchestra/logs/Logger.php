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
abstract class Logger extends FileHandler
{
   private static $logDIR;
   private static $logFile;

   public static function set_log_directory($path)
   {
      self::$logDIR = $path;
   }

   public static function create_log_folder()
   {
      $log_folder = (new FileHandler())->getProjectRoot() . self::$logDIR;

      if (!is_dir($log_folder)) {
         if (!mkdir($log_folder, 0777, true)) {
            throw new Exception("Could not create log directory");
         }
      }

      self::$logFile = $log_folder . 'error.log';
      if (!file_exists(self::$logFile)) {
         if (!touch(self::$logFile)) {
            throw new Exception("Could not create log file");
         }
      }
   }

   public static function get_log_dir(): string
   {
      return self::getProjectRoot() . self::$logFile  . 'error.log';
   }

   /**
    * Convenience function that handles outputting
    * data to a log file for debugging
    */
   public static function write($message, $endpoint = "",)
   {
      $timestamp = date('Y-m-d H:i:s');

      $callingScript = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'];

      $formattedLogMessage = "[$timestamp]: endpoint: $endpoint, Controller: $callingScript, message: $message" . PHP_EOL;

      $result = file_put_contents(self::$logFile, $formattedLogMessage, FILE_APPEND);
      if ($result === false) {
         return false;
      }

      return true;
   }
}
