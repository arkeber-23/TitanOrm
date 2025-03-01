<?php
namespace Cabez\TitanOrm\Database\Attributes\Relations;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ManyToOne
{
    public function __construct(
        public string $targetEntity,   
        public ?string $onDelete = 'CASCADE',
        public ?string $onUpdate = 'CASCADE',
        public ?string $nameRelation = null,
        public bool $nullable = false
    ) {}
} 