<?php


declare(strict_types=1);

namespace Orchestra\bandwidth\exception;

class TimeoutException extends LockAcquireException
{
    /**
     * Creates a new instance of the TimeoutException class.
     *
     * @param int $timeout The timeout in seconds.
     * @return self A timeout has been exceeded exception.
     */
    public static function create(int $timeout): self
    {
        return new self(\sprintf('Timeout of %d seconds exceeded.', $timeout));
    }
}