<?php

namespace Orchestra\cli\command;

use Orchestra\cli\handlers\ControllerHandler;
use Orchestra\cli\handlers\MigrationHandler;
use Orchestra\cli\handlers\ModelHandler;

class CLI
{
   private $command;
   private $arguments;
   private $handler;

   private Command $cmd;

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
         case 'migrate':
            $this->handleMigrateCommand($subCommand);
            break;
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
         case 'model':
            $this->handler = new ModelHandler($this->arguments[0]);
            break;
         case 'migration':
            $this->handler = new MigrationHandler($this->arguments[0]);
            break;
         default:
            echo "Unknown make command: $subCommand\n";
      }
   }

   private function handleMigrateCommand($subCommand)
   {
      switch ($subCommand) {
         case 'up':
            MigrationHandler::migrate();
            break;
         case 'down':
            MigrationHandler::rollback();
            break;
         default:
            echo "Unknown migrate command: $subCommand\n";
      }
   }
}
