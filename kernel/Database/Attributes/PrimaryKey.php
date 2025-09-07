<?php
namespace Cabez\TitanOrm\Kernel\Database\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PrimaryKey
{
    public function __construct(
        public ?string $name = null,
        public bool $autoIncrement = true,
        public bool $nullable = false,
        public int $order = 1
    ) {}
} 