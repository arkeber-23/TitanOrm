<?php
namespace Cabez\Titan\Struct;

use Cabez\Titan\Struct\Interfaces\IStruct;


class Struct implements IStruct
{
  public static function create(string $name, callable $callback)
  {
    if(is_callable($callback)){
        call_user_func($callback, $name);
    }

  }

  public static function drop(string $name)
  {
    // TODO: Implement drop() method.
  }
}