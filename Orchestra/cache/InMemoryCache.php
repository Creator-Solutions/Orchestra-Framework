<?php

namespace Orchestra\cache;

class InMemoryCache implements CacheInterface
{
   private $cache = [];

   public function set(string $key, $value, int $ttl = 3600): bool
   {
      $this->cache[$key] = [
         'value' => $value,
         'expires' => time() + $ttl
      ];
      return true;
   }

   public function get(string $key)
   {
      if (isset($this->cache[$key])) {
         $item = $this->cache[$key];
         if (time() < $item['expires']) {
            return $item['value'];
         }
         // Cache expired
         unset($this->cache[$key]);
      }
      return null;
   }

   public function delete(string $key): bool
   {
      if (isset($this->cache[$key])) {
         unset($this->cache[$key]);
         return true;
      }
      return false;
   }

   public function clear(): bool
   {
      $this->cache = [];
      return true;
   }
}
