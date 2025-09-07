<?php
namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Kernel\Database\Attributes\Entity;
use Cabez\TitanOrm\Kernel\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Kernel\Database\Attributes\Column;
use Cabez\TitanOrm\Kernel\Database\Migrations\Migration;
use Cabez\TitanOrm\Kernel\Interfaces\TypeData;


#[Entity(name: 'course', schema: 'public')]
class Course extends Migration
{
    #[PrimaryKey(name: 'id')]
    public int $id;

    #[Column(type: TypeData::VARCHAR, name: 'name', length: 100, nullable: false, order: 2)]
    public string $name;

    #[Column(type: TypeData::VARCHAR, name: 'description', length: 255, nullable: true, order: 3)]
    public string $description;

    #[Column(type: TypeData::INTEGER, name: 'credits', nullable: false, order: 4)]
    public int $credits;


} 