<?php

namespace Cabez\TitanOrm\Kernel\Database\Schema;

use Cabez\TitanOrm\Kernel\Database\Attributes\Column;
use Cabez\TitanOrm\Kernel\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Kernel\Database\DataBaseFactory;
use Cabez\TitanOrm\Kernel\Database\Relations\RelationshipHandler;

class ColumnDefinitionBuilder
{
    private string $driver;
    private RelationshipHandler $relationshipHandler;

    public function __construct(string $driver)
    {
        $this->driver = $driver;
        $this->relationshipHandler = new RelationshipHandler();
    }

    public function getColumnDefinition(\ReflectionProperty $property): ?string
    {
        // Clave Primaria
        $primaryKey = $property->getAttributes(PrimaryKey::class);
        if (!empty($primaryKey)) {
            return $this->buildPrimaryKeyDefinition($property, $primaryKey[0]->newInstance());
        }

        // Columna Regular
        $column = $property->getAttributes(Column::class);
        if (!empty($column)) {
            return $this->buildColumnDefinition($property, $column[0]->newInstance());
        }

        return null;
    }

    private function buildPrimaryKeyDefinition(\ReflectionProperty $property, $primaryKeyAttr): string
    {
        $columnName = $primaryKeyAttr->name ?? $property->getName();
        return sprintf(
            "\t %s %s",
            $columnName,
            DataBaseFactory::getPrimaryKeyType($this->driver)
        );
    }

    private function buildColumnDefinition(\ReflectionProperty $property, $columnAttr): string
    {
        $type = $columnAttr->type;
        if ($columnAttr->length) {
            $type .= "({$columnAttr->length})";
        }

        $columnName = $columnAttr->name ?? $property->getName();

        return sprintf(
            "\t %s %s%s%s",
            $columnName,
            $type,
            $columnAttr->nullable ? '' : ' NOT NULL',
            $columnAttr->default ? " DEFAULT {$columnAttr->default}" : ''
        );
    }
}
