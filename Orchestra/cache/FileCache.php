<?php

namespace Orchestra\cache;


class FileCache implements CacheInterface
{
   private $cacheDir;

   public function __construct(string $cacheDir)
   {
      $this->cacheDir = $cacheDir;
      if (!is_dir($cacheDir)) {
         mkdir($cacheDir, 0777, true);
      }
   }

   private function getFilePath(string $key): string
   {
      return $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.cache';
   }

   public function set(string $key, $value, int $ttl = 3600): bool
   {
      $filePath = $this->getFilePath($key);
      $data = [
         'value' => $value,
         'expires' => time() + $ttl
      ];
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
