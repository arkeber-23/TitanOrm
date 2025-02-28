<?php
namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Database\Model;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Interfaces\TypeData;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
class Persona extends Model
{
    #[PrimaryKey(order: 1)]
    protected int $id;
   #[Column(TypeData::VARCHAR,name: "nombre_persona", length: 150, order: 2)]
   protected string $nombre;
   #[Column(TypeData::VARCHAR, length: 100,  order: 3)]
   protected string $apellido;
   #[Column(TypeData::VARCHAR, length: 100,  order: 4)]
   protected string $email;
   #[Column(TypeData::VARCHAR, length: 100,  order: 5)]
   protected string $telefono;
   #[Column(TypeData::VARCHAR, length: 100,  order: 6)]
   protected string $direccion;
   #[Column(TypeData::VARCHAR, length: 100, order: 7)]
   protected string $ciudad;
}