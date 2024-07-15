<?php

namespace Cabez\TitanOrm\Database;

class DataBaseFactory
{

    public static function getDriveConection(): string
    {
        return self::getUrlConection($_ENV['DB_DRIVER']);
    }
    
    private static function getUrlConection(string $driver):string
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db_name = $_ENV['DB_NAME'];
        $db_charset = $_ENV['DB_CHARSET'];
        
        switch ($driver) {
            case "mysql":
                return "mysql:host={$host};port={$port};dbname={$db_name};charset={$db_charset}";
            case "pgsql":
                return "pgsql:host={$host};port={$port};dbname={$db_name};";
            default:
                throw new \InvalidArgumentException("Unsupported driver: {$driver}");
        }
    }

}