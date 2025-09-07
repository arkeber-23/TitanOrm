<?php
namespace Cabez\TitanOrm\Kernel\Database\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity {
    public function __construct(
        public string $name,
        public ?string $schema = null
        ) {}
}