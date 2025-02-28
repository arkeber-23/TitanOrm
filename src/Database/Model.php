<?php
namespace Cabez\TitanOrm\Database;

use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Interfaces\TypeData;

abstract class Model
{

    public static function getTableName(): string
    {
        $className = (new \ReflectionClass(static::class))->getShortName();
        return strtolower($className) . 's';
    }
} 