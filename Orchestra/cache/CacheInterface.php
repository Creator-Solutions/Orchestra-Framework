<?php

namespace Orchestra\cache;

interface CacheInterface
{
   public function set(string $key, $value, int $ttl = 3600): bool;
   public function get(string $key);
   public function delete(string $key): bool;
   public function clear(): bool;
}
