<?php
namespace Cabez\TitanOrm\Database\Attributes\Relations;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class OneToMany
{
    public function __construct(
        public string $targetEntity,    
        public string $mappedBy) {}
} 