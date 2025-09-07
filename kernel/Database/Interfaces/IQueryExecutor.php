<?php
namespace Cabez\TitanOrm\Kernel\Database\Interfaces;

interface IQueryExecutor
{
    /**
     * Creates a new table based on the given class name
     */
    public function createTable(Array $className): void;

    /**
     * Drops a table based on the given class name
     */
    public function dropTable(string $className): void;

    /**
     * Adds a new column to an existing table
     */
    public function addColumn(string $className, string $columnName, string $dataType): void;

    /**
     * Removes a column from an existing table
     */
    public function removeColumn(string $className, string $columnName): void;
} 