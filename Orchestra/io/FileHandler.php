<?php

namespace Orchestra\io;

class FileHandler{

    /**
     * @var string
     */
    private string $rootProjectFolder;

    public function getProjectRoot(): string{
        // Get the directory of the current PHP script
        $currentDirectory = __DIR__;

        // Define the depth to go up in the directory structure to reach the root folder
        $depthToRoot = 0;

        // Specify the name of the root project folder
        $rootFolderName = 'founders-vaporium';

        // Keep moving up in the directory structure until you reach the root folder
        while ($depthToRoot < 10) { // Limit the number of iterations for safety
            if (basename($currentDirectory) === $rootFolderName) {
                // Found the root project folder
                $this->rootProjectFolder = $currentDirectory;
                break;
            }

            // Move up one directory level
            $currentDirectory = dirname($currentDirectory);
            $depthToRoot++;
        }

        return $this->rootProjectFolder;
    }

}