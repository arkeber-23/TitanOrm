<?php

namespace Cabez\TitanOrm\Kernel\Database;

use PDO;
use Exception;

class Connection
{
    private static ?PDO $conn = null;
    private static string $driver;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (is_null(self::$conn)) {
            $db_user = $_ENV['DB_USER'];
            $db_password = $_ENV['DB_PASSWORD'];
            self::$driver = $_ENV['DB_DRIVER'];

                try {
                    $dsn = DataBaseFactory::getDriveConection();
                    $optionsConfig = self::getDriverOptions();
                    self::$conn = new PDO($dsn, $db_user, $db_password, $optionsConfig);
            } catch (\Exception $e) {
                throw new Exception("Error in the connection database: " . $e->getMessage());
            }
        }
        return self::$conn;
    }

    private static function getDriverOptions(): array
    {
        $commonOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        return match(self::$driver) {
            'mysql' => array_merge($commonOptions, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_PERSISTENT => true
            ]),
            'pgsql' => array_merge($commonOptions, [
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]),
            'sqlite' => $commonOptions,
            'sqlsrv' => array_merge($commonOptions, [
                PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
            ]),
            default => $commonOptions
        };
    }

    public static function getDriver(): string
    {
        if (!isset(self::$driver)) {
            self::$driver = $_ENV['DB_DRIVER'];
        }
        return self::$driver;
    }

    public static function closeConnection(): void
    {
        if (self::$conn !== null) {
            self::$conn = null;
        }
    }

    public function __clone()
    {
        throw new Exception("Cannot clone a singleton object.");
    }

    public function __destruct()
    {
        self::closeConnection();
    }
}