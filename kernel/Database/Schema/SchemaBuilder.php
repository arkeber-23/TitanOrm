<?php
namespace Cabez\TitanOrm\Kernel\Database\Schema;

use Cabez\TitanOrm\Kernel\Database\Attributes\Entity;
use ReflectionClass;

class SchemaBuilder
{
    private string $driver;
    private PropertyAnalyzer $propertyAnalyzer;
    private ColumnDefinitionBuilder $columnBuilder;

    public function __construct(string $driver)
    {

        $this->driver = $driver;
        $this->propertyAnalyzer = new PropertyAnalyzer();
        $this->columnBuilder = new ColumnDefinitionBuilder($driver);
                
    }

    public function createTableDefinition(string $className): string
    {

        $reflection = new ReflectionClass($className);
        $tableInfo = $this->getTableInfo($reflection);
        $columns = $this->getColumnDefinitions($reflection);


        
        $nameSchema = $tableInfo['schema'] ? "{$tableInfo['schema']}.{$tableInfo['tableName']}" : $tableInfo['tableName'];
        $createSchema = $tableInfo['schema'] ? "CREATE SCHEMA IF NOT EXISTS {$tableInfo['schema']};\n \n" : "";
       
        return "{$createSchema}CREATE TABLE IF NOT EXISTS {$nameSchema} (\n" .
            implode(",\n", $columns) .
            "\n);\n\n";
    }

    private function getTableInfo(ReflectionClass $reflection): array
    {
        $tableName = $reflection->getMethod('getTableName')->invoke(null);
        $entityAttributes = $reflection->getAttributes(Entity::class);
        
        $schema = null;
        if (!empty($entityAttributes)) {
            $entity = $entityAttributes[0]->newInstance();
            $tableName = $entity->name ?? $tableName;
            $schema = $entity->schema ?? null;
        }
        
        return ['tableName' => $tableName, 'schema' => $schema];
    }

    private function getColumnDefinitions(ReflectionClass $reflection): array
    {
        $properties = $this->propertyAnalyzer->getPropertiesInOrder($reflection);
        $columns = [];

        foreach ($properties as $property) {
            $columnDef = $this->columnBuilder->getColumnDefinition($property);
            if ($columnDef) {
                $columns[] = $columnDef;
            }
        }

        return $columns;
    }
}