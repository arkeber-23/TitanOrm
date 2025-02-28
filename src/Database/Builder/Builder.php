<?php
namespace Cabez\TitanOrm\Database\Builder;

use Cabez\TitanOrm\Database\Connection;
use PDO;

use Cabez\TitanOrm\Database\Interfaces\IBuilder;
use PDOException;

class Builder implements IBuilder
{
    private $connection;
    private $query;
    private $bindings = [];

    public function __construct()
    {
        $this->connection = Connection::getConnection();
        $this->query = '';
    }

    public static function builder()
    {
        return new self();
    }

    public function select(...$columns)
    {
        $columns = empty($columns) ? '*' : implode(', ', $columns);
        $this->query = "SELECT $columns";
        return $this;
    }

    public function from(string $table)
    {
        $this->query .= " FROM $table";
        return $this;
    }

    public function where(string $column, string $operator, string $value,string $condition = 'AND')
    {
            $prefix = strpos($this->query, 'WHERE') === false ? ' WHERE' : " $condition";
            $this->query .= "$prefix $column $operator ?";
            $this->bindings[] = $value;
            return $this;
    }

    public function whereGroup(callable $callback, string $condition = 'AND')
    {
        $prefix = strpos($this->query, 'WHERE') === false ? ' WHERE (' : " $condition (";
        $this->query .= $prefix;
        
        $subBuilder = new self();
        $callback($subBuilder);
        $this->query .= substr($subBuilder->query, 7) . ')'; 
        $this->bindings = array_merge($this->bindings, $subBuilder->bindings);
        
        return $this;
    }

    public function between(string $column, string $firstValue, string $secondValue){
        $prefix = strpos($this->query, 'WHERE') === false ? ' WHERE' : ' AND';
        $this->query .= "$prefix $column BETWEEN ? AND ?";
        $this->bindings[] = $firstValue;
        $this->bindings[] = $secondValue;
        return $this;
        
    }

    public function orderBy(string $column, string $order = 'ASC')
    {
        $this->query .= " ORDER BY $column $order";
        return $this;
    }

    public function limit(int $limit)
    {
        $this->query .= " LIMIT ?";
        $this->bindings[] = $limit;
        return $this;
    }

    public function build()
    {
        $this->query.=';';
        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}