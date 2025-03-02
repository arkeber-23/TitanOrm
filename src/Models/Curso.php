<?php
namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Database\Attributes\Entity;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Database\Migrations\Migration;
use Cabez\TitanOrm\Interfaces\TypeData;
use Cabez\TitanOrm\Database\Attributes\Relations\ManyToMany;

#[Entity(name: 'cursos')]
class Curso extends Migration
{
    #[PrimaryKey(name: 'id')]
    public int $id;

    #[Column(type: TypeData::VARCHAR, name: 'name', length: 100, nullable: false, order: 2)]
    public string $name;

    #[Column(type: TypeData::VARCHAR, name: 'description', length: 255, nullable: true, order: 3)]
    public string $description;

    #[Column(type: TypeData::INTEGER, name: 'credits', nullable: false, order: 4)]
    public int $credits;

    #[ManyToMany(targetEntity: Persona::class, inversedBy: "cursos")]
    public array $estudiantes = [];

} 