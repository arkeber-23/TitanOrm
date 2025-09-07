<?php
namespace Cabez\TitanOrm\Kernel\Database\Interfaces;


interface IBuilder
{
    public function select(...$columns);
    public function from(string $table);
    public function where(string $column, string $operator, string $value, string $condition = 'AND');
    public function whereGroup(callable $callback, string $condition = 'AND');
    public function orderBy(string $column, string $order = 'ASC');
    public function between(string $column, string $firtsValue, string $secondValue);
    public function limit(int $limit);
    public function build();
}