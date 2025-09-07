<?php

namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Kernel\Database\Migrations\Migration;
use Cabez\TitanOrm\Kernel\Interfaces\TypeData;
use Cabez\TitanOrm\Kernel\Database\Attributes\Column;
use Cabez\TitanOrm\Kernel\Database\Attributes\Entity;
use Cabez\TitanOrm\Kernel\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Kernel\Database\Attributes\Relations\ManyToMany;

#[Entity(name: "citie", schema: "cities")]
class City extends Migration{

    #[PrimaryKey(name: "id", autoIncrement: true)]
    public int $id;
    #[Column(TypeData::VARCHAR, name: "name", length: 100,order: 2)]
    public string $name;
    #[Column(TypeData::VARCHAR, name: "country", length: 100,order: 3)]
    public string $country;

    #[ManyToMany(targetEntity: Person::class, nameRelation: "id")]
    public Person $person;

}