<?php
namespace Cabez\TitanOrm\Kernel\Database\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $type,
        public ?string $name = null,
        public ?int $length = null,
        public bool $nullable = true,
        public bool $unique = false,
        public ?string $default = null,
        public int $order = 999
    ) {}
} 