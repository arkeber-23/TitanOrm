<?php
namespace Cabez\TitanOrm\Database;

use Cabez\TitanOrm\Database\Interfaces\IQueryExecutor;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Relations\ForeignKey;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToOne;
use Cabez\TitanOrm\Database\Attributes\Relations\OneToMany;

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

private function createRelationTables(string $className): void
{
    $reflection = new ReflectionClass($className);
    
    foreach ($reflection->getProperties() as $property) {
        $manyToMany = $property->getAttributes(ManyToOne::class);
        if (!empty($manyToMany)) {
            $this->createManyToManyTable($className, $property, $manyToMany[0]->newInstance());
        }
    }
}

private function createManyToManyTable(string $sourceClass, ReflectionProperty $property, ManyToOne $relation): void
{
    $sourceTable = (new ReflectionClass($sourceClass))->getMethod('getTableName')->invoke(null);
    $targetTable = (new ReflectionClass($relation->targetEntity))->getMethod('getTableName')->invoke(null);
    
    $tableName = "{$sourceTable}_{$targetTable}";
    
    $query = "CREATE TABLE IF NOT EXISTS {$tableName} (
        {$sourceTable}_id INTEGER REFERENCES {$sourceTable}(id) ON DELETE CASCADE,
        {$targetTable}_id INTEGER REFERENCES {$targetTable}(id) ON DELETE CASCADE,
        PRIMARY KEY ({$sourceTable}_id, {$targetTable}_id)
    )";
    echo $query;
    //$this->connection->exec($query);
}


private function handleManyToOneRelation(ReflectionProperty $property, ManyToOne $relation): ?string
{
    $targetClass = new ReflectionClass($relation->targetEntity);
    $targetTable = $targetClass->getMethod('getTableName')->invoke(null);
    
    $foreignKeyName = $property->getName() . '_id';
    
    return sprintf(
        "%s INTEGER%s REFERENCES %s(%s) ON DELETE %s ON UPDATE %s",
        $foreignKeyName,
        $relation->nullable ? '' : ' NOT NULL',
        $targetTable,
        $relation->nameRelation ?? 'id',
        $relation->onDelete ?? 'CASCADE',
        $relation->onUpdate ?? 'CASCADE'
    );
}


public function createTable(string $className): void
{
    
    $reflection = new ReflectionClass($className);
    $tableName = $reflection->getMethod('getTableName')->invoke(null);
 
    $columns = $this->getColumnDefinitions($reflection);
    
    $query = "CREATE TABLE IF NOT EXISTS {$tableName} (\n" . 
            implode(",\n", $columns) . 
            "\n)";

   $this->connection->exec($query);




   
    // Crear índices únicos
   $this->createUniqueIndexes($tableName, $reflection);
    
    // Crear tablas de relaciones
    //$this->createRelationTables($className);
}

} 