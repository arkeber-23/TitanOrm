<?php

namespace Cabez\Titan\Struct\Interfaces;

interface IStruct
{
    public static function create(string $name, callable $callback);
    public static function drop(string $name);
}