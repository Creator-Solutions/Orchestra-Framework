<?php

namespace Orchestra\forte;

use Orchestra\bandwidth\exception\PhpLockException;
use RuntimeException;

class ThrowableException extends RuntimeException implements PhpLockException
{

   public const HEADERS_NOT_SET = 1;
   public const USER_NOT_AUTHENTICATED = 2;
   public const UNKNOWN_USER_AGENT = 3;
}