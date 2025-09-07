<?php 
namespace Cabez\TitanOrm\Kernel\Database\Schema;

use Cabez\TitanOrm\Kernel\Database\Attributes\Column;
use Cabez\TitanOrm\Kernel\Database\Attributes\PrimaryKey;
use ReflectionClass;

class PropertyAnalyzer
{
    public function getPropertiesInOrder(ReflectionClass $reflection): array
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

    private function getPropertyOrder(\ReflectionProperty $property): int
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
}