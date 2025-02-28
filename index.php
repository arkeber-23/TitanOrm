<?php
use Cabez\TitanOrm\Database\Builder\Builder;
use Cabez\TitanOrm\Database\QueryExecutor;
use Cabez\TitanOrm\Models\Persona;
use Cabez\TitanOrm\Models\Cliente;
use Cabez\TitanOrm\Models\Usuario;
require_once './vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
try {

   /* $data = Builder::builder()
        ->select("*")
        ->from("public.clientes")
        ->build();
    echo json_encode($data);*/
        $query = new QueryExecutor();
        $query->createTable(Persona::class);
        $query->createTable(Cliente::class);
        $query->createTable(Usuario::class);

} catch (Exception $e) {
    echo "Error de conexión " . $e->getMessage();
}
