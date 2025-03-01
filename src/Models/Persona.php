<?php

namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Database\Attributes\Entity;
use Cabez\TitanOrm\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Database\Attributes\Column;
use Cabez\TitanOrm\Database\Migrations\Migration;
use Cabez\TitanOrm\Interfaces\TypeData;

#[Entity(name: 'personas')]
class Persona extends Migration
{
    #[PrimaryKey(name: 'id')]
    public int $id;

    #[Column(type:TypeData::VARCHAR,name: 'name_person',nullable: true,length: 50,order: 2)]
    public string $name;    

    #[Column(type:TypeData::VARCHAR,name: 'last_name',nullable: true,length: 50,order: 3)]
    public string $lastName;    
}