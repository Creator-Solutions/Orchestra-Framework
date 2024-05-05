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

   const INTERNAL_ERROR = "INTERNAL_ERROR";
   const EXCEPTION = "EXCEPTION CAUGHT";
   const INFORMATION = "INFO";
   const WARNING = "WARN";
}
