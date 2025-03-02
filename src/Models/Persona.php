<?php

namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Database\Attributes\Entity;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Database\Migrations\Migration;
use Cabez\TitanOrm\Interfaces\TypeData;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToOne;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToMany;

#[Entity(name: 'personas')]
class Persona extends Migration
{
    #[PrimaryKey(name: 'id')]
    public int $id;

    #[Column(type: TypeData::VARCHAR, name: 'name_person', nullable: true, length: 50, order: 2)]
    public string $name;    

    #[Column(type: TypeData::VARCHAR, name: 'last_name', nullable: true, length: 50, order: 3)]
    public string $lastName;

    #[ManyToOne(targetEntity: Eber::class)]
    #[Column(type: TypeData::INTEGER,name:'tipo_documento_id',nullable: true, order: 4)]
    public array $tipoDocumento = [];

    #[ManyToMany(targetEntity: Curso::class, mappedBy: "estudiantes")]
    public array $cursos = [];

  
}