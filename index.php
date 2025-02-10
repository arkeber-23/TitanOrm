<?php
use Cabez\TitanOrm\Database\Builder\Builder;
require_once './vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
try {
    $data = Builder::builder()
        ->select("*")
        ->from("planificacion.vw_proyectos_pdot")
        ->where("id","=","132")
        ->orderBy("id","DESC")
        ->build();
    echo json_encode($data);
} catch (Exception $e) {
    echo "Error de conexión " . $e->getMessage();
}
