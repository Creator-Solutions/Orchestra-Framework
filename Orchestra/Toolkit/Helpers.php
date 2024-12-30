<?php

use app\Models\User;
use Orchestra\env\EnvConfig;
use Exception;
use Orchestra\forte\ForteException;

function now($timezone = null)
{
    $env = new EnvConfig();
    // Set timezone from .env if not provided
    $timezone = $timezone ?? $env->getenv("TIMEZONE");
    $date = new \DateTime('now', new \DateTimeZone($timezone));
    return $date->format('Y-m-d H:i:s');
}