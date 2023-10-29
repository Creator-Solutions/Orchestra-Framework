<?php

namespace Orchestra\bandwidth;

use Orchestra\bandwidth\storage\StorageException;
use Orchestra\bandwidth\exception\MutexException;
use LengthException;

use Orchestra\util\tokenconverter\TokenConverter;
use Orchestra\bandwidth\storage\FileStorage as Storage;

class TokenBucket
{

    /**
     * @var int
     */
    private int $bucketCapacity;

    /**
     * @var Rate
     */
    private Rate $rate;

    /**
     * @var
     */
    private $storage;

    /**
     * @var 
     */
    private static $instance;

    /**
     * @var TokenConverter Token converter.
     */
    private $tokenConverter;

    public function __construct(int $capacity, $rate, $storage = null){
        if ($capacity <= 0){
            throw new \InvalidArgumentException("Capacity should exceed 0");
        }



        $this->bucketCapacity = $capacity;
        $this->rate           = $rate;
        $this->storage        = $storage;  
        
        $this->tokenConverter = new TokenConverter($rate);
    }

    public static function getInstance(int $capacity, $rate, $storage){
        if (!self::$instance){
            self::$instance = new self($capacity, $rate, $storage);            
        }
    }

    public function bootstrap($tokens = 0){
        try{

            if ($tokens > $this->bucketCapacity){
                throw new \LengthException(
                    "Initial token amount ($tokens) is larger than the capacity ($this->bucketCapacity)"
                );
            }

            if ($tokens < 0){
                throw new \InvalidArgumentException(
                    "Initial token amount ($tokens) should be greater than 0"
                );
            }

        }catch (MutexException $e){
            throw new StorageException("Could not lock bootstrapping", 0, $e);
        }
    }

        /**
     * Consumes tokens from the bucket.
     *
     * This method consumes only tokens if there are sufficient tokens available.
     * If there aren't sufficient tokens, no tokens will be removed and the
     * remaining seconds to wait are written to $seconds.
     *
     * This method is threadsafe.
     *
     * @param int    $tokens   The token amount.
     * @param double &$seconds The seconds to wait.
     *
     * @return bool If tokens were consumed.
     * @SuppressWarnings(PHPMD)
     *
     * @throws \LengthException The token amount is larger than the capacity.
     * @throws StorageException The stored microtime could not be accessed.
     */
    public function consume($tokens, &$seconds = 0)
    {
        try {
            if ($tokens > $this->bucketCapacity) {
                throw new \LengthException("Token amount ($tokens) is larger than the capacity ($this->bucketCapacity).");
            }
            if ($tokens <= 0) {
                throw new \InvalidArgumentException("Token amount ($tokens) should be greater than 0.");
            }

            if (null !== $this->storage){
                return $this->storage->getMutex()->synchronized(
                    function () use ($tokens, &$seconds) {
                        $tokensAndMicrotime = $this->loadTokensAndTimestamp();
                        $microtime = $tokensAndMicrotime["microtime"];
                        $availableTokens = $tokensAndMicrotime["tokens"];
    
                        $delta = $availableTokens - $tokens;
                        if ($delta < 0) {
                            $this->storage->letMicrotimeUnchanged();
                            $passed  = microtime(true) - $microtime;
                            $seconds = max(0, $this->tokenConverter->convertTokensToSeconds($tokens) - $passed);
                            return false;
                        } else {
                            $microtime += $this->tokenConverter->convertTokensToSeconds($tokens);
                            $this->storage->setMicrotime($microtime);
                            $seconds = 0;
                            return true;
                        }
                    }
                );
            }
        } catch (MutexException $e) {
            throw new StorageException("Could not lock token consumption.", 0, $e);
        }
    }

       /**
     * Returns the token add rate.
     *
     * @return Rate The rate.
     */
    public function getRate()
    {
        return $this->rate;
    }
    
    /**
     * The token capacity of this bucket.
     *
     * @return int The capacity.
     */
    public function getCapacity()
    {
        return $this->bucketCapacity;
    }

    /**
     * Returns the currently available tokens of this bucket.
     *
     * This is a purely informative method. Use this method if you are
     * interested in the amount of remaining tokens. Those tokens
     * could be consumed instantly. This method will not consume any token.
     * Use {@link consume()} to do so.
     *
     * This method will never return more than the capacity of the bucket.
     *
     * @return int amount of currently available tokens
     * @throws StorageException The stored microtime could not be accessed.
     */
    public function getTokens()
    {
        return $this->loadTokensAndTimestamp()["tokens"];
    }
    
    /**
     * Loads the stored timestamp and its respective amount of tokens.
     *
     * This method is a convenience method to allow sharing code in
     * {@link TokenBucket::getTokens()} and {@link TokenBucket::consume()}
     * while accessing the storage only once.
     *
     * @throws StorageException The stored microtime could not be accessed.
     * @return array tokens and microtime
     */
    private function loadTokensAndTimestamp()
    {
        $microtime = $this->storage->getMicrotime();
        
        // Drop overflowing tokens
        $minMicrotime = $this->tokenConverter->convertTokensToMicrotime($this->bucketCapacity);
        if ($minMicrotime > $microtime) {
            $microtime = $minMicrotime;
        }
        
        $tokens = $this->tokenConverter->convertMicrotimeToTokens($microtime);
        return [
            "tokens" => $tokens,
            "microtime" => $microtime
        ];
    }
}