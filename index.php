<?php

use Cabez\TitanOrm\Database\QueryExecutor;

require_once './vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
try {

    $query = new QueryExecutor();
    $files =  glob("./src/Models/*.php");
    foreach ($files as $file) {
        $class = pathinfo($file, PATHINFO_FILENAME);
        $namespace = "Cabez\\TitanOrm\\Models\\" . $class;
        if (class_exists($namespace)) {
            $query->createTable($namespace);
        } else {
            echo "No se pudo crear la tabla";
        }
    }
} catch (Exception $e) {
    echo "Error de conexión " . $e->getMessage();
}
