<?php

namespace Orchestra\cache;

class CacheFactory
{
   public static function createCache(string $type, $options = null)
   {
      switch ($type) {
         case 'memory':
            return new InMemoryCache();
         case 'file':
            print_r("File type select");
            if (is_string($options)) {
               return new FileCache($options);
            }
            throw new \InvalidArgumentException('Cache directory must be specified for file caching.');
         default:
            throw new \InvalidArgumentException('Unsupported cache type.');
      }
   }
}
