<?php

namespace Cabez\TitanOrm\Kernel\Database;

use Cabez\TitanOrm\Kernel\Database\Interfaces\IQueryExecutor;
use Cabez\TitanOrm\Kernel\Database\Migrations\MigrationGenerator;
use Cabez\TitanOrm\Kernel\Database\Relations\RelationshipHandler;
use Cabez\TitanOrm\Kernel\Database\Schema\SchemaBuilder;
use Cabez\TitanOrm\Kernel\Database\Indexes\IndexManager as IndexesIndexManager;
use ReflectionClass;

/**
 * Clase principal que coordina la ejecución de consultas DDL
 */
class QueryExecutor implements IQueryExecutor
{
    private $connection;
    private string $driver;
    private SchemaBuilder $schemaBuilder;
    private RelationshipHandler $relationshipHandler;
    private MigrationGenerator $migrationGenerator;
    private IndexesIndexManager $indexManager;

    public function __construct()
    {
        $this->connection = Connection::getConnection();
        $this->driver = Connection::getDriver();

        $this->schemaBuilder = new SchemaBuilder($this->driver);
        $this->relationshipHandler = new RelationshipHandler();
        $this->migrationGenerator = new MigrationGenerator();
        $this->indexManager = new IndexesIndexManager($this->connection);
    }

    public function createAllTablesFromFolder(string $folderPath): void
    {
        $files = glob($folderPath . '/*.php');
        $classes = [];

        foreach ($files as $file) {
            $class = pathinfo($file, PATHINFO_FILENAME);
            $namespace = "Cabez\\TitanOrm\\Models\\" . $class;
            if (class_exists($namespace)) {
                $classes[] = $namespace;
            }
        }

        $this->createTable($classes);
    }

    public function createTable(array $classNames): void
    {
        $allRelationQueries = [];

        foreach ($classNames as $className) {

            $tableDefinition = $this->schemaBuilder->createTableDefinition($className);
            $this->migrationGenerator->addMigration($tableDefinition);

            $reflection = new \ReflectionClass($className);
            $tableName = $reflection->getMethod('getTableName')->invoke(null);

            $this->indexManager->createUniqueIndexes($tableName, $reflection);

            // Ahora revisamos las relaciones después de crear la tabla principal.
            if ($this->relationshipHandler->hasRelations($className)) {
                $relationTables = $this->relationshipHandler->createRelationTables($className);
                $allRelationQueries = array_merge($allRelationQueries, $relationTables);
            }
        }

        foreach ($allRelationQueries as $relationQuery) {
            $this->migrationGenerator->addMigration($relationQuery);
        }
    }

    public function dropTable(string $className): void
    {
        $tableName = (new ReflectionClass($className))->getMethod('getTableName')->invoke(null);
        $query = "DROP TABLE IF EXISTS {$tableName}";
        $this->connection->exec($query);
    }

    public function addColumn(string $className, string $columnName, string $dataType): void
    {
        $tableName = (new ReflectionClass($className))->getMethod('getTableName')->invoke(null);
        $query = "ALTER TABLE {$tableName} ADD COLUMN IF NOT EXISTS {$columnName} {$dataType}";
        $this->connection->exec($query);
    }

    public function removeColumn(string $className, string $columnName): void
    {
        $tableName = (new ReflectionClass($className))->getMethod('getTableName')->invoke(null);
        $query = "ALTER TABLE {$tableName} DROP COLUMN IF EXISTS {$columnName}";
        $this->connection->exec($query);
    }
}
