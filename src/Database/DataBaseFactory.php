<?php

namespace Cabez\TitanOrm\Database;

class DataBaseFactory
{
    public const MYSQL = 'mysql';
    public const PGSQL = 'pgsql';
    public const SQLITE = 'sqlite';
    public const SQLSRV = 'sqlsrv';

    private static array $supportedDrivers = [
        self::MYSQL,
        self::PGSQL,
        self::SQLITE,
        self::SQLSRV
    ];

    public static function getDriveConection(): string
    {
        $driver = $_ENV['DB_DRIVER'];
        self::validateDriver($driver);
        return self::getUrlConection($driver);
    }
    
    private static function getUrlConection(string $driver): string
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db_name = $_ENV['DB_NAME'];
        $db_charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        
        switch ($driver) {
            case self::MYSQL:
                return "mysql:host={$host};port={$port};dbname={$db_name};charset={$db_charset}";
            case self::PGSQL:
                return "pgsql:host={$host};port={$port};dbname={$db_name}";
            case self::SQLITE:
                return "sqlite:" . ($_ENV['DB_PATH'] ?? __DIR__ . '/database.sqlite');
            case self::SQLSRV:
                return "sqlsrv:Server={$host},{$port};Database={$db_name}";
            default:
                throw new \InvalidArgumentException("Driver no soportado: {$driver}");
        }
    }

    private static function validateDriver(string $driver): void
    {
        if (!in_array($driver, self::$supportedDrivers)) {
            throw new \InvalidArgumentException(
                "Driver no soportado: {$driver}. Drivers soportados: " . 
                implode(', ', self::$supportedDrivers)
            );
        }
        
    }
    public static function getPrimaryKeyType(string $driver): string
    {
        return match ($driver) {
            self::MYSQL => 'INT AUTO_INCREMENT ',
            self::PGSQL => 'BIGSERIAL PRIMARY KEY ',
            self::SQLITE => 'INTEGER PRIMARY KEY AUTOINCREMENT ',    
            default => throw new \InvalidArgumentException("Driver no soportado: {$driver}"),
        };
    }

}