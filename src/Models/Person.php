<?php

namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Kernel\Database\Attributes\Column;
use Cabez\TitanOrm\Kernel\Database\Attributes\Entity;
use Cabez\TitanOrm\Kernel\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Kernel\Database\Migrations\Migration;
use Cabez\TitanOrm\Kernel\Interfaces\TypeData;

#[Entity(name: 'person', schema: 'public')]
class Person extends Migration
{

    #[PrimaryKey(name: 'id', autoIncrement: true)]
    public int $id;

    #[Column(type: TypeData::VARCHAR, name: 'number_id', length: 255, order: 2)]
    public string $numberId;

    #[Column(type: TypeData::VARCHAR, name: 'name', length: 255, order: 3)]
    public string $name;

    #[Column(type: TypeData::VARCHAR, name: 'lastname', length: 255, order: 4)]
    public string $lastname;

    #[Column(type: TypeData::VARCHAR, name: 'email', length: 255, unique: true, order: 5)]
    public string $email;

    #[Column(type: TypeData::VARCHAR, name: 'phone', length: 10, order: 6)]
    public string $phone;

    #[Column(type: TypeData::VARCHAR, name: 'address', length: 255, order: 7)]
    public string $address;


}
