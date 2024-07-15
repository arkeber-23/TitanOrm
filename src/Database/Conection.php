<?php

namespace Cabez\TitanOrm\Database;

use Exception;
use PDO;
class Conection
{

    private static $conn = null;

    private function __construct()
    {

    }

    public static function getConnection(): PDO
    {
        $db_user = $_ENV['DB_USER'];
        $db_password = $_ENV['DB_PASSWORD'];
        
        if (is_null(self::$conn)) {
            self::$conn = new Conection();
        }
        $optionsConfig = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $dsn = DataBaseFactory::getDriveConection();
            self::$conn = new PDO($dsn, $db_user, $db_password, $optionsConfig);
        } catch (\Exception $e) {
            throw new Exception("Error in th connection database " . $e->getMessage());
        }
        return self::$conn;
    }

    public function __clone()
    {

    }


}