<?php
namespace Cabez\TitanOrm\Kernel\Database\Attributes\Relations;


#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ForeignKey
{
    public function __construct(
        public string $references,      
        public ?string $onDelete = 'CASCADE',
        public ?string $onUpdate = 'CASCADE',
        public int $order = 999
    ) {}
} 