<?php

namespace Orchestra\cache;

use Orchestra\io\FileHandler;
use Orchestra\logs\Logger;
use Orchestra\logs\LogTypes;
use Orchestra\env\EnvConfig;



class FileCache implements CacheInterface
{
   private $cacheDir;
   private $env;

   public function __construct()
   {
      $this->env = new EnvConfig();

      $this->cacheDir = $this->env->getenv("CACHE_FOLDER");

      // Check if the directory exists
      if (!is_dir($this->cacheDir)) {
         // Attempt to create the directory
         if (mkdir($this->cacheDir, 0777, true)) {
            echo "Directory created successfully: $this->cacheDir";
         } else {
            echo "Failed to create directory: $this->cacheDir";
         }
      } else {
         Logger::write("Directory already exists: $this->cacheDir", LogTypes::INFORMATION);
      }
   }

   private function getFilePath(string $key): string
   {
      $filePath = (new FileHandler)->getProjectRoot() . $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.cache';

      // Log the file path being accessed
      error_log("Attempting to write to: $filePath");

      return $filePath;
   }

   public function set(string $key, $value, int $ttl = 3600): bool
   {
      $filePath = $this->getFilePath($key);
      $data = [
         'value' => $value,
         'expires' => time() + $ttl
      ];

      // Check if the cache directory is writable
      if (!is_writable($this->cacheDir)) {
         echo "Cache directory is not writable: {$this->cacheDir}\n";
         return false;
      }

      return file_put_contents($filePath, serialize($data)) !== false;
   }

   public function get(string $key)
   {
      $filePath = $this->getFilePath($key);
      if (file_exists($filePath)) {
         $data = unserialize(file_get_contents($filePath));
         if (time() < $data['expires']) {
            return $data['value'];
         }
         // Cache expired
         unlink($filePath);
      }
      return null;
   }

   public function delete(string $key): bool
   {
      $filePath = $this->getFilePath($key);
      if (file_exists($filePath)) {
         unlink($filePath);
         return true;
      }
      return false;
   }

   public function clear(): bool
   {
      $files = glob($this->cacheDir . DIRECTORY_SEPARATOR . '*.cache');
      foreach ($files as $file) {
         unlink($file);
      }
      return true;
   }
}
