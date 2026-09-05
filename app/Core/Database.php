<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
                self::$connection = new PDO($dsn, DB_USER, DB_PASS);

                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );
                self::$connection->setAttribute(
                    PDO::ATTR_EMULATE_PREPARES,
                    false
                );
                self::$connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );
                self::$connection->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                self::$connection->exec("SET character_set_client = utf8mb4");
                self::$connection->exec("SET character_set_connection = utf8mb4");
                self::$connection->exec("SET character_set_results = utf8mb4");
            } catch (PDOException $e) {
                // First attempt failed; try a fallback to 127.0.0.1 in case 'localhost' socket/pipe issues exist
                error_log('[Database] initial connect failed: ' . $e->getMessage());
                try {
                    $fallbackDsn = "mysql:host=127.0.0.1;port=3306;dbname=".DB_NAME.";charset=utf8mb4";
                    self::$connection = new PDO($fallbackDsn, DB_USER, DB_PASS);
                    // success
                } catch (PDOException $e2) {
                    error_log('[Database] fallback connect failed: ' . $e2->getMessage());
                    throw $e2;
                }
            }
        }

        return self::$connection;
    }
}
