<?php
namespace Cabez\TitanOrm\Models;
use Cabez\TitanOrm\Database\Model;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Interfaces\TypeData;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToOne;
use Cabez\TitanOrm\Models\Cliente;

class Usuario extends Model
{
    #[PrimaryKey(order: 1)]
    protected int $id;
    #[Column(TypeData::VARCHAR, length: 100, nullable: false, order: 2)]
    protected string $nombre;
    #[Column(TypeData::VARCHAR, length: 100, nullable: false, order: 3)]
    protected string $email;
    #[Column(TypeData::VARCHAR, length: 100, nullable: false, order: 4)]
    protected string $password;
    #[ManyToOne(targetEntity: Cliente::class, nameRelation: 'cliente_id')]
    #[Column(TypeData::INTEGER, nullable: true, order: 5)]
    protected Cliente $cliente;
}
