<?php

namespace Cabez\TitanOrm\Kernel\Database\Indexes;

use Cabez\TitanOrm\Kernel\Database\Attributes\Column;
use ReflectionClass;

class IndexManager
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function createUniqueIndexes(string $tableName, ReflectionClass $reflection): void
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
}