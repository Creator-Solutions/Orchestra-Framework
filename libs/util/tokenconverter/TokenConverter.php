<?php

namespace Orchestra\util\tokenconverter;

use Orchestra\bandwidth\Rate;

/**
 * Class that handles token conversion
 */
final class TokenConverter
{

    /**
     * @var Rate
     */
    private Rate $rate;

    /**
     * @var int precision scale for bc_* operations
     */
    private $bcScale = 8;

    public function __construct(Rate $rate){
        $this->rate = $rate;
    }

    /**
     * Converts a duration of seconds into an amount of tokens
     * @param double $seconds, the duration in seconds
     * @return int Number of Tokens
     */
    public function convertSecondsToTokens($seconds)
    {
        return (int) ($seconds * $this->rate->getTokensPerSecond());
    }

     /**
     * Converts an amount of tokens into a duration of seconds.
     *
     * @param int $tokens The amount of tokens.
     * @return double The seconds.
     */
    public function convertTokensToSeconds($tokens)
    {
        return $tokens / $this->rate->getTokensPerSecond();
    }
    
    /**
     * Converts an amount of tokens into a timestamp.
     *
     * @param int $tokens The amount of tokens.
     * @return double The timestamp.
     */
    public function convertTokensToMicrotime($tokens)
    {
        return microtime(true) - $this->convertTokensToSeconds($tokens);
    }
    
    /**
     * Converts a timestamp into tokens.
     *
     * @param double $microtime The timestamp.
     *
     * @return int The tokens.
     */
    public function convertMicrotimeToTokens($microtime)
    {
        $delta = bcsub(microtime(true), $microtime, $this->bcScale);
        return $this->convertSecondsToTokens($delta);
    }
}