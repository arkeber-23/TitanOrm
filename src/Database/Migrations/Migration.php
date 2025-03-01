<?php
namespace Cabez\TitanOrm\Database\Migrations;


class Migration
{
    public static function getTableName(): string
    {
        
        $className = (new \ReflectionClass(static::class))->getShortName();
        
        return strtolower($className) . 's';
    }
}