<?php

namespace Cabez\TitanOrm\Generator\Interfaces;

interface IGenerator
{   
    public function sql(string $name, ...$columns):string;
}
