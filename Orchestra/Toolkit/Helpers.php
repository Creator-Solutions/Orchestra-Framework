<?php

use Orchestra\env\EnvConfig;

function now($timezone = null)
{
    $env = new EnvConfig();
    // Set timezone from .env if not provided
    $timezone = $timezone ?? $env->getenv("TIMEZONE");
    $date = new \DateTime('now', new \DateTimeZone($timezone));
    return $date->format('Y-m-d H:i:s');
}

function getIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
        // Handle IPv6 localhost
        return $ip === '::1' ? '127.0.0.1' : $ip;
    }
    return 'UNKNOWN';
}

function getUserAgent(): string
{
    return $_SERVER['HTTP_USER_AGENT'] ?? "";
}

function env($key): string
{
    $env = new EnvConfig();
    $env->parse();
    return $env->getenv($key);
}

function detectOperatingSystem()
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($userAgent, 'Windows') !== false) {
        return 'Windows';
    } elseif (strpos($userAgent, 'Mac') !== false) {
        return 'MacOS';
    } elseif (strpos($userAgent, 'Linux') !== false) {
        return 'Linux';
    } elseif (strpos($userAgent, 'iPhone') !== false) {
        return 'iOS';
    } elseif (strpos($userAgent, 'Android') !== false) {
        return 'Android';
    }
    return 'Unknown OS';
}

function detectBrowser()
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browsers = [
        'Firefox' => 'Firefox',
        'Chrome' => 'Chrome',
        'Safari' => 'Safari',
        'Opera' => 'Opera',
        'MSIE' => 'Internet Explorer',
        'Trident' => 'Internet Explorer', // IE 11+
        'Edge' => 'Edge',
    ];

    foreach ($browsers as $key => $name) {
        if (strpos($userAgent, $key) !== false) {
            return $name;
        }
    }

    return 'Unknown Browser';
}