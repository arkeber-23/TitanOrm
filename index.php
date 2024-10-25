<?php


use Cabez\TitanOrm\Controller\HomeController;


require_once './vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
try{
$test = \Cabez\TitanOrm\Database\Conection::getConnection();

$sql = "SELECT * FROM test";

$consulta = $test->query($sql);
$consulta->execute();
$dtos = $consulta->fetchAll(PDO::FETCH_OBJ);
var_dump($dtos);

}catch (Exception $e){
    echo "Error de conexión ". $e->getMessage();
}