<?php
namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Database\Model;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Interfaces\TypeData;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToOne;
class Cliente extends Model
{

    #[PrimaryKey(order: 1,name: 'cliente_id')]
   protected int $id;
   #[Column(TypeData::VARCHAR, length: 100, nullable: false, order: 2)]
   protected string $nombre;
   #[Column(TypeData::VARCHAR, length: 100, nullable: false, unique: true, order: 3)]
   protected string $email;
   #[Column(TypeData::INTEGER, nullable: true, order: 4)]
   protected ?int $edad;
   #[Column(TypeData::VARCHAR, length: 20, nullable: true, order: 5)]
   protected ?string $telefono;
   #[Column(TypeData::VARCHAR, length: 20, nullable: true,name: 'nombre_completo' , order: 5)]
   protected ?string $nombreCompleto;
   #[ManyToOne(targetEntity: Persona::class, nameRelation: 'id')]
   #[Column(TypeData::INTEGER, nullable: true, order: 6)]
   protected Persona $pesona;
   #[Column(TypeData::VARCHAR, length: 100, nullable: false, order: 7)]
   protected string $direccion;

    
} 