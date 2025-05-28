<?php

namespace Orchestra\logs;

/**
 * ----------------------
 * Log Line Types
 * ----------------------
 * 
 * Displayes different "Message" formats in 
 * the log file. 
 * 
 * Allows the user to specify the type of log line
 * without having to implement the signing themselves
 * 
 * Allows for easy debugging as each log line will display
 * the type of log line it is, i.e. errors or warnings
 * 
 * @author founderstud\owen
 */
interface LogTypes
{

   public const INTERNAL_ERROR = "INTERNAL_ERROR";
   public const EXCEPTION = "EXCEPTION";
   public const INFORMATION = "INFO";
   public const WARNING = "WARN";
   public const DEBUG = "DEBUG";

   public const SQL_EXECUTION = "SQL_SCRIPT";
   public const FOLDER_MKDIR = "FOLDER_MKDIR";
   public const PIPELINE = "PIPELINE";
}
