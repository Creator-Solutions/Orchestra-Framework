<?php

namespace Orchestra\database;

use \PDO;
use \Exception;
use Orchestra\env\EnvConfig;

/**
 *  Main class to handle database connection
 * @author Owen
 */
class DatabaseHelper
{
    public static ?PDO $conn = null;

    /**
     *  Function to initialize database connection.
     * @throws Exception
     */
    public static function init()
    {
        $env = new EnvConfig();

        if (self::$conn === null) {
            try {
                self::$conn = new PDO($env->getenv('DB_CONNECTION').":host=" . $env->getenv('DB_HOST') . ';dbname=' . $env->getenv('DB_DATABASE'), $env->getenv('DB_USERNAME'), $env->getenv('DB_PASSWORD'));
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }
    }
}
