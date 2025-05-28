<?php

namespace Orchestra\io;

class FileHandler
{

    /**
     * @var string
     */
    private string $rootProjectFolder;

    public function getProjectRoot(): string
    {
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

    public function get_mail_template($templateName)
    {
        $template = $this->getProjectRoot() . "/app/resources/views/$templateName.pulse.php";

        if (!file_exists($template)) {
            return "";
        }

        return file_get_contents($template);
    }

    public function getServerRoot(): string
    {
        $current_dir = __DIR__;

        $depthToRoot = 0;

        // Specify the number of levels to go up
        $levelsToRoot = 3;

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