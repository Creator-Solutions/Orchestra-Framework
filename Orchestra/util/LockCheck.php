<?php

namespace Orchestra\bandwidth\util;

use Orchestra\bandwidth\mutex\Mutex;

/**
 * The double-checked locking pattern.
 *
 * (c) @author Owen Burns
 * 
 * @author Founder Studios -> Owen Burns
 * @author Creator-Solution -> Owen Burns
 */
class LockCheck
{

    /**
     * @var Mutex
     */
    private Mutex $mutex;

    /**
     * @var callable
     */
    private $check;

    /**
     * @param Orchestra\mutex\Mutex $mutex provides methods for exclusive 
     * code execution
     * @param cllable $check Callback that decides if the lock should be
     * acquired and if the critical code callback should be executed after
     * acquiring the lock
     */
    public function __construct(Mutex $mutex, callable $check)
    {
        $this->mutex = $mutex;
        $this->check = $check;
    }

    public function then(callable $code)
    {
        if (!\call_user_func($this->check)) {
            return false;
        }

        return $this->mutex->synchronized(function () use ($code) {
            if (!\call_user_func($this->check)) {
                return false;
            }

            return $code;
        });
    }
}
