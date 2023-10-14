<?php

namespace Orchestra\bandwidth\mutex;

use Orchestra\bandwidth\util\LockCheck;

/**
 * Abstract Mutex class
 * Mutual Exclusion -> only a single instance
 * is permitted to use a resource
 */
abstract class Mutex
{

    /**
     * @param callable -> callable code after checking is done
     */
    abstract public function synchronized(callable $code);


    /**
     * @param callable desides if lock should be acquired 
     * and if the sync callback should run after acquiring 
     * the lock
     * @return Orchestra\util\LockCheck;
     */
    public function check(callable $check): LockCheck
    {
        return new LockCheck($this, $check);
    }
}