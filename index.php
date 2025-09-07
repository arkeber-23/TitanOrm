<?php

use Cabez\TitanOrm\Kernel\Database\Connection;
use Cabez\TitanOrm\Kernel\Database\QueryExecutor;

require_once './vendor/autoload.php';


$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


try {
    $query = new QueryExecutor();
    
    $query->createAllTablesFromFolder('./src/Models');
} catch (Exception $e) {
    echo "Error de conexión: " . $e->getMessage();
}
