<?php
namespace Cabez\TitanOrm\Kernel\Database\Attributes\Relations;


#[\Attribute(\Attribute::TARGET_PROPERTY)]
class OneToMany
{
    public function __construct(
        public string $targetEntity,
        public string $mappedBy,
        public ?string $onDelete = 'CASCADE',
        public ?string $onUpdate = 'CASCADE',
        public ?string $nameRelation = null,
        public bool $orphanRemoval = false
    ) {}
} 