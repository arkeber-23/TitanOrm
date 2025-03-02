<?php
namespace Cabez\TitanOrm\Database\Attributes\Relations;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ManyToMany
{
    public function __construct(
        public string $targetEntity,
        public ?string $joinTable = null,
        public ?string $inversedBy = null,
        public ?string $mappedBy = null,
        public ?string $onDelete = 'CASCADE',
        public ?string $onUpdate = 'CASCADE',
        public ?string $nameRelation = null,
        public bool $nullable = false
    ) {}
} 