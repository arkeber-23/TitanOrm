<?php
namespace Cabez\TitanOrm\Kernel\Generator;

use Cabez\TitanOrm\Generator\Interfaces\IGenerator;

class MysqlGenerator implements IGenerator{


    public function sql(string $name, ...$columns):string
    {
        return '';
    }


}