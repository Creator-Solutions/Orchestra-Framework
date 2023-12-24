<?php

declare(strict_types=1);

namespace Orchestra\bandwidth\exception;

use RuntimeException;
use Orchestra\bandwidth\exception\PhpLockException;

/**
 * Mutex exception.
 *
 * Generic exception for any other not covered reason. Usually extended by
 * child classes.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @link bitcoin:1P5FAZ4QhXCuwYPnLZdk3PJsqePbu1UDDA Donations
 * @license WTFPL
 */
class MutexException extends RuntimeException implements PhpLockException
{
    /**
     * @var int Not enough redis servers.
     */
    public const REDIS_NOT_ENOUGH_SERVERS = 1;
}
