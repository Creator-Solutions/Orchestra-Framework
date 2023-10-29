<?php

namespace Orchestra\util;

use \Exception;

class RetryHandler
{

  public function retryOperation($maxRetries, $function, $delayBetweenRetries = 1000)
  {
    $attempts = 0;

    do {
      try {
        $function();
        return; // Operation succeeded, exit the loop
      } catch (Exception $e) {
        echo "Attempt $attempts failed: " . $e->getMessage() . PHP_EOL;
      }

      if ($attempts < $maxRetries) {
        // Delay before the next retry
        usleep($delayBetweenRetries * 1000); // Convert to microseconds
      }

      $attempts++;
    } while ($attempts <= $maxRetries);

    echo "Operation failed after $maxRetries attempts." . PHP_EOL;
  }
}
