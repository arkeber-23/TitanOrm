<?php

namespace Cabez\TitanOrm\Database;

use Cabez\TitanOrm\Database\Interfaces\IQueryExecutor;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Relations\ForeignKey;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToOne;
use Cabez\TitanOrm\Database\Attributes\Relations\OneToMany;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToMany;
use Cabez\TitanOrm\Database\Attributes\Entity;
use ReflectionClass;
use ReflectionProperty;

class QueryExecutor implements IQueryExecutor
{
    private $connection;
    private string $driver;

    public function __construct()
    {
        $this->connection = Connection::getConnection();
        $this->driver = Connection::getDriver();
    }


    private function getColumnDefinitions(ReflectionClass $reflection): array
    {
        $properties = $this->getPropertiesInOrder($reflection);
        $columns = [];


        foreach ($properties as $property) {
            $columnDef = $this->getColumnDefinition($property);
            if ($columnDef) {
                $columns[] = $columnDef;
            }
        }

        return $columns;
    }

    private function getPropertiesInOrder(ReflectionClass $reflection): array
    {
        $properties = [];

        do {
            $classProperties = $reflection->getProperties();
            foreach ($classProperties as $property) {
                $order = $this->getPropertyOrder($property);
                $properties[$order] = $property;
            }
        } while ($reflection = $reflection->getParentClass());

        ksort($properties);
        return $properties;
    }

    private function getPropertyOrder(ReflectionProperty $property): int
    {
        $primaryKey = $property->getAttributes(PrimaryKey::class);
        if (!empty($primaryKey)) {
            return $primaryKey[0]->newInstance()->order;
        }

        $column = $property->getAttributes(Column::class);
        if (!empty($column)) {
            return $column[0]->newInstance()->order;
        }

        return 999;
    }

    private function getColumnDefinition(ReflectionProperty $property): ?string
    {
        $primaryKey = $property->getAttributes(PrimaryKey::class);
        if (!empty($primaryKey)) {
            $columnName = $primaryKey[0]->newInstance()->name ?? $property->getName();
            return sprintf(
                "%s %s",
                $columnName,
                DataBaseFactory::getPrimaryKeyType($this->driver)
            );
        }

        $manyToOne = $property->getAttributes(ManyToOne::class);
        if (!empty($manyToOne)) {
            return $this->handleManyToOneRelation(
                $property,
                $manyToOne[0]->newInstance()
            );
        }

        $oneToOne = $property->getAttributes(OneToOne::class);
        if (!empty($oneToOne)) {
            return $this->handleOneToOneRelation($property, $oneToOne[0]->newInstance());
        }

        $column = $property->getAttributes(Column::class);
        if (empty($column)) {
            return null;
        }

        $columnAttr = $column[0]->newInstance();
        $type = $columnAttr->type;

        if ($columnAttr->length) {
            $type .= "({$columnAttr->length})";
        }

        $columnName = $columnAttr->name ?? $property->getName();

        return sprintf(
            "%s %s%s%s",
            $columnName,
            $type,
            $columnAttr->nullable ? '' : ' NOT NULL',
            $columnAttr->default ? " DEFAULT {$columnAttr->default}" : ''
        );
    }

    private function createUniqueIndexes(string $tableName, ReflectionClass $reflection): void
    {
        foreach ($reflection->getProperties() as $property) {
            $column = $property->getAttributes(Column::class);
            if (!empty($column)) {
                $columnAttr = $column[0]->newInstance();
                if ($columnAttr->unique) {
                    $indexName = "{$tableName}_{$property->getName()}_unique";
                    $query = "CREATE UNIQUE INDEX IF NOT EXISTS {$indexName} ON {$tableName} ({$property->getName()})";

                    $this->connection->exec($query);
                }
            }
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

    public function createTable(string $className): void
    {
        $reflection = new ReflectionClass($className);
        $tableName = $reflection->getMethod('getTableName')->invoke(null);
        $namesEntity = $reflection->getAttributes(Entity::class);

        $schema = null;

        if (!empty($namesEntity)) {
            $classAttr = $namesEntity[0]->newInstance();
            $tableName = $classAttr->name ?? "";
            $schema = $classAttr->schema ?? "";
        }

        $columns = $this->getColumnDefinitions($reflection);
        $nameSchema = $schema ? "{$schema}." . $tableName : $tableName;
        $createSchema = $schema ? "CREATE SCHEMA IF NOT EXISTS {$schema};" : "";

        $query = "{$createSchema} CREATE TABLE IF NOT EXISTS {$nameSchema} (\n" .
            implode(",\n", $columns) .
            "\n);\n\n";

        $this->createFileMigration($query);

        // Crear índices únicos
        $this->createUniqueIndexes($tableName, $reflection);

        // Crear tablas de relaciones solo si hay relaciones ManyToMany
        $hasManyToMany = false;
        foreach ($reflection->getProperties() as $property) {
            if (!empty($property->getAttributes(ManyToMany::class))) {
                $hasManyToMany = true;
                break;
            }
        }

        if ($hasManyToMany) {
            $this->createRelationTables($className);
        }
    }

    private function createRelationTables(string $className): void
    {
        $reflection = new ReflectionClass($className);
        foreach ($reflection->getProperties() as $property) {
            $manyToMany = $property->getAttributes(ManyToMany::class);
            if (!empty($manyToMany)) {
                $relation = $manyToMany[0]->newInstance();
                // Solo crear la tabla de unión desde el lado propietario (el que tiene inversedBy)
                if (isset($relation->inversedBy)) {
                    $this->createManyToManyTable($className, $property, $relation);
                }
            }
        }
    }

    private function createManyToManyTable(string $sourceClass, ReflectionProperty $property, ManyToMany $relation): void
    {
        $sourceTable = (new ReflectionClass($sourceClass))->getMethod('getTableName')->invoke(null);
        $targetTable = (new ReflectionClass($relation->targetEntity))->getMethod('getTableName')->invoke(null);

        $tableName = $relation->joinTable ?? "{$sourceTable}_{$targetTable}";
        
        $query = "CREATE TABLE IF NOT EXISTS {$tableName} (
            id BIGSERIAL PRIMARY KEY,
            {$sourceTable}_id INTEGER NOT NULL REFERENCES {$sourceTable}(id) ON DELETE CASCADE,
            {$targetTable}_id INTEGER NOT NULL REFERENCES {$targetTable}(id) ON DELETE CASCADE,
            UNIQUE({$sourceTable}_id, {$targetTable}_id)
        );\n\n";

        $this->createFileMigration($query);
    }

    private function handleOneToOneRelation(ReflectionProperty $property, OneToOne $relation): string
    {
        $targetClass = new ReflectionClass($relation->targetEntity);
        $targetTable = $targetClass->getMethod('getTableName')->invoke(null);
        
        
        $foreignKeyName = $property->getName() . '_id';

        return sprintf(
            "%s INTEGER%s REFERENCES %s(%s) ON DELETE %s ON UPDATE %s UNIQUE",
            $foreignKeyName,
            $relation->nullable ? '' : ' NOT NULL',
            $targetTable,
            $relation->nameRelation ?? 'id',
            $relation->onDelete ?? 'CASCADE',
            $relation->onUpdate ?? 'CASCADE'
        );
    }

    private function handleManyToOneRelation(ReflectionProperty $property, ManyToOne $relation): string
    {
        $targetClass = new ReflectionClass($relation->targetEntity);
        $targetTable = $targetClass->getMethod('getTableName')->invoke(null);
        $nameEntity = $targetClass->getAttributes(Entity::class);
        $propertyColumn = $property->getAttributes(Column::class);
        $nameTable =  $nameEntity[0]->newInstance()->name??$targetTable;
        $foreignKeyName = $propertyColumn[0]->newInstance()->name ??  $property->getName() . '_id';
        return sprintf(
            "%s INTEGER%s REFERENCES %s(%s) ON DELETE %s ON UPDATE %s",
            $foreignKeyName,
            $relation->nullable ? '' : ' NOT NULL',
            $nameTable,
            $relation->nameRelation ?? 'id',
            $relation->onDelete ?? 'CASCADE',
            $relation->onUpdate ?? 'CASCADE'
        );
    }

    private function createFileMigration(string $query): void
    {
        $createFile = "./src/Database/SQL/migrations.sql";
        if (!file_exists($createFile)) {
            mkdir("./src/Database/SQL", 0777, true);
        }
        $file = fopen($createFile, "a");
        fwrite($file, $query);
        fclose($file);
    }
}
