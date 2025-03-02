<?php
namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Database\Attributes\Entity;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Database\Migrations\Migration;
use Cabez\TitanOrm\Interfaces\TypeData;
use Cabez\TitanOrm\Database\Attributes\Relations\OneToMany;

#[Entity(name:'tipo_documento')]
class Eber extends Migration
{
    #[PrimaryKey(name:'id')]
    public int $id;

    #[Column(type:TypeData::VARCHAR,name: 'name',length:100,nullable:false,order:2)]
    public string $name;

    #[Column(type:TypeData::VARCHAR,name:'description',length:255,nullable:false,order:3)]
    public string $description;

    #[OneToMany(targetEntity: Persona::class, mappedBy: "tipo_documento")]
    public array $personas = [];

}