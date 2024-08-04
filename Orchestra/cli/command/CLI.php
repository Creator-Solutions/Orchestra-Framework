<?php

namespace Orchestra\cli\command;

use Orchestra\cli\handlers\ControllerHandler;

class CLI
{
   private $command;
   private $arguments;
   private $handler;

   public function __construct($command, $arguments)
   {
      $this->command = $command;
      $this->arguments = $arguments;
   }

   public function execute()
   {
      $commandParts = explode(':', $this->command);
      $mainCommand = $commandParts[0];
      $subCommand = $commandParts[1] ?? null;

      switch ($mainCommand) {
         case 'make':
            $this->handleMakeCommand($subCommand);
            break;
            // Add more commands as needed
         default:
            echo "Unknown command: $mainCommand\n";
      }
   }

   private function handleMakeCommand($subCommand)
   {
      switch ($subCommand) {
         case 'controller':
            $this->handler = new ControllerHandler($this->arguments[0]);

            break;
            // Add more sub-commands as needed
         default:
            echo "Unknown make command: $subCommand\n";
      }
   }
}
