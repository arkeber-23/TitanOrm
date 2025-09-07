<?php
namespace Cabez\TitanOrm\Kernel\Database\Attributes\Relations;


#[\Attribute(\Attribute::TARGET_PROPERTY)]
class OneToOne
{
    public function __construct(
        public string $targetEntity,
        public ?string $mappedBy = null,
        public ?string $onDelete = 'CASCADE',
        public ?string $onUpdate = 'CASCADE',
        public ?string $nameRelation = null,
        public bool $nullable = false
    ) {}
} 