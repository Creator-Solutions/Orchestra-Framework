<?php

namespace Orchestra\storage;

use \PDO;
use \Exception;

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
    public static function initMySQL()
    {
        $config = parse_ini_file('config.ini');

        if (self::$conn === null) {
            try {
                self::$conn = new PDO("mysql:host=" . $config['host'] . ';dbname=' . $config['db'], $config['user'], $config['password']);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }
    }

    public static function initPG()
    {
        $config = parse_ini_file('config.ini');

        if (self::$conn === null) {
            try {
                // Connect to PostgreSQL with username and password
                self::$conn = new PDO("pgsql:host=" . $config['host'] . ';port=5432;dbname=' . $config['db'], $config['user'], $config['password']);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }
    }
}
