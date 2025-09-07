<?php

namespace Cabez\TitanOrm\Kernel\Database\Migrations;

use Cabez\TitanOrm\Kernel\Database\Connection as DatabaseConnection;

class MigrationGenerator
{
    private string $migrationPath;


    public function __construct()
    {
        $migrationPath = './src/migrations/';
        if (!dir($migrationPath)) {
            mkdir($migrationPath, 0777, true);
        }

        $filename = $migrationPath . 'migration_' . date('Y_m_d_h_i_s') . '.sql';

        if (file_exists($filename)) unlink($filename);
        $this->migrationPath = $filename;
    }

    public function addMigration(string $query): void
    {

        $this->createFileMigration($query);
        $this->executeQuery($query);
    }

    private function createFileMigration(string $query): void
    {
        if (file_exists($this->migrationPath)) {
            $existingContent = file_get_contents($this->migrationPath);
            if (strpos($existingContent, $query) !== false) {
                return;
            }


            $file = fopen($this->migrationPath, "a");
            fwrite($file, "\n\n" . $query);
        } else {
            // Crear nuevo archivo
            $file = fopen($this->migrationPath, "w");
            fwrite($file, $query);
        }
        fclose($file);
    }

    public function createMigrationFile(string $className): string
    {
        $timestamp = date('Y_m_d_His');
        $simpleName = basename(str_replace('\\', '/', $className));
        return "migration_{$timestamp}_create_{$simpleName}.sql";
    }

    private function executeQuery($sql)
    {
        try {
            DatabaseConnection::getConnection()->exec($sql);
        } catch (\Exception $e) {
            echo "\e[91mError executing query: \n" . $e->getMessage() . "\n";
        }
    }
}
