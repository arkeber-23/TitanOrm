<?php

namespace Cabez\TitanOrm\Kernel\Database\Relations;

use Cabez\TitanOrm\Kernel\Database\Attributes\Entity;
use Cabez\TitanOrm\Kernel\Database\Attributes\Relations\ManyToMany;
use Cabez\TitanOrm\Kernel\Database\Attributes\Relations\ManyToOne;
use Cabez\TitanOrm\Kernel\Database\Attributes\Relations\OneToMany;
use Cabez\TitanOrm\Kernel\Database\Attributes\Relations\OneToOne;
use ReflectionClass;
use ReflectionProperty;

class RelationshipHandler
{
    /**
     * Revisa si una clase tiene alguna relación definida.
     */
    public function hasRelations(string $className): bool
    {

        if (!class_exists($className)) {
            return false;
        }

        $reflection = new ReflectionClass($className);
        
        foreach ($reflection->getProperties() as $property) {
            $relationAttributes = array_merge(
                $property->getAttributes(OneToOne::class),
                $property->getAttributes(OneToMany::class),
                $property->getAttributes(ManyToMany::class),
                $property->getAttributes(ManyToOne::class)
            );

            if (!empty($relationAttributes)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Crea las consultas SQL para las tablas de relaciones.
     */
    public function createRelationTables(string $className): array
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("La clase '$className' no existe.");
        }

        $reflection = new ReflectionClass($className);
        $queries = [];

        // Validar la existencia del atributo Entity y obtener el nombre de la tabla de la entidad principal
        $entityAttr = $reflection->getAttributes(Entity::class);


        if (empty($entityAttr)) {
            throw new \LogicException("La clase '$className' no tiene el atributo Entity.");
        }
        $entityInstance = $entityAttr[0]->newInstance();
        
        $tableName = $entityInstance->name;
        $schemaName = $entityInstance->schema ?? 'public';
        $fullTableName = $schemaName . '.' . $tableName;


        foreach ($reflection->getProperties() as $property) {
            $relationAttr = $this->getRelationAttribute($property);

            if ($relationAttr === null) {
                continue;
            }

            $relation = $relationAttr->newInstance();

            switch (true) {
                case $relation instanceof ManyToMany:
                    $queries[] = $this->handleManyToManyRelation($reflection, $property, $relation);
                    break;
                case $relation instanceof ManyToOne:
                    $queries[] = $this->handleManyToOneRelation($property, $relation, $fullTableName);
                    break;
                case $relation instanceof OneToOne:
                    $queries[] = $this->handleOneToOneRelation($property, $relation, $fullTableName);
                    break;
                case $relation instanceof OneToMany:
                    $queries[] = $this->handleOneToManyRelation($property, $relation);
                    break;
            }
        }

        return $queries;
    }

    /**
     * Función auxiliar para obtener el primer atributo de relación de una propiedad.
     */
    private function getRelationAttribute(ReflectionProperty $property): ?\ReflectionAttribute
    {
        $attributes = $property->getAttributes(ManyToOne::class);
        if (!empty($attributes)) {
            return $attributes[0];
        }
        $attributes = $property->getAttributes(OneToMany::class);
        if (!empty($attributes)) {
            return $attributes[0];
        }
        $attributes = $property->getAttributes(ManyToMany::class);
        if (!empty($attributes)) {
            return $attributes[0];
        }
        $attributes = $property->getAttributes(OneToOne::class);
        if (!empty($attributes)) {
            return $attributes[0];
        }
        return null;
    }

    /**
     * Genera la consulta ALTER TABLE para una relación ManyToOne.
     */
    public function handleManyToOneRelation(ReflectionProperty $property, $relation, string $tableName): string
    {
        $targetClass = new ReflectionClass($relation->targetEntity);
        $targetEntityAttr = $targetClass->getAttributes(Entity::class);
        $targetTable = $targetEntityAttr[0]->newInstance()->name;

        $foreignKeyName = $property->getName() . '_id';

        return sprintf(
            "ALTER TABLE %s ADD COLUMN %s INTEGER%s REFERENCES %s(%s) ON DELETE %s ON UPDATE %s;",
            $tableName,
            $foreignKeyName,
            $relation->nullable ? '' : ' NOT NULL',
            $targetTable,
            $relation->nameRelation ?? 'id',
            $relation->onDelete ?? 'CASCADE',
            $relation->onUpdate ?? 'CASCADE'
        );
    }

    /**
     * Genera la consulta ALTER TABLE para una relación OneToMany.
     */
    public function handleOneToManyRelation(ReflectionProperty $property, $relation): string
    {
        $targetClass = new ReflectionClass($relation->targetEntity);
        $targetEntityAttr = $targetClass->getAttributes(Entity::class);
        $targetTable = $targetEntityAttr[0]->newInstance()->name;
        
        // En una relación OneToMany, la clave foránea se añade en la tabla del lado 'Many' (targetEntity).
        $foreignKeyName = $targetClass->getShortName() . '_id';

        return sprintf(
            "ALTER TABLE %s ADD COLUMN %s INTEGER REFERENCES %s(%s) ON DELETE %s ON UPDATE %s;",
            $targetTable,
            $foreignKeyName,
            $property->getName() ?? 'id', // La clave primaria de la tabla 'One'
            $relation->nameRelation ?? 'id',
            $relation->onDelete ?? 'CASCADE',
            $relation->onUpdate ?? 'CASCADE'
        );
    }

    /**
     * Genera la consulta ALTER TABLE para una relación OneToOne.
     */
    public function handleOneToOneRelation(ReflectionProperty $property, $relation, string $tableName): string
    {
        $targetClass = new ReflectionClass($relation->targetEntity);
        $targetEntityAttr = $targetClass->getAttributes(Entity::class);
        $targetTable = $targetEntityAttr[0]->newInstance()->name;
        
        $foreignKeyName = $property->getName() . '_id';

        return sprintf(
            "ALTER TABLE %s ADD COLUMN %s INTEGER%s REFERENCES %s(%s) ON DELETE %s ON UPDATE %s UNIQUE;",
            $tableName,
            $foreignKeyName,
            $relation->nullable ? '' : ' NOT NULL',
            $targetTable,
            $relation->nameRelation ?? 'id',
            $relation->onDelete ?? 'CASCADE',
            $relation->onUpdate ?? 'CASCADE'
        );
    }

    /**
     * Genera la consulta CREATE TABLE para una relación ManyToMany.
     */
    public function handleManyToManyRelation(ReflectionClass $sourceReflection, ReflectionProperty $property, $relation): string
    {
        $sourceEntityAttr = $sourceReflection->getAttributes(Entity::class);
        $sourceTable = $sourceEntityAttr[0]->newInstance()->name;
        
        $targetClass = new ReflectionClass($relation->targetEntity);
        $targetEntityAttr = $targetClass->getAttributes(Entity::class);
        $targetTable = $targetEntityAttr[0]->newInstance()->name;

        $tableName = $relation->joinTable ?? "{$sourceTable}_{$targetTable}";

        return "CREATE TABLE IF NOT EXISTS {$tableName} (
            id BIGSERIAL PRIMARY KEY,
            {$sourceTable}_id INTEGER NOT NULL REFERENCES {$sourceTable}(id) ON DELETE CASCADE,
            {$targetTable}_id INTEGER NOT NULL REFERENCES {$targetTable}(id) ON DELETE CASCADE,
            UNIQUE({$sourceTable}_id, {$targetTable}_id)
        );\n\n";
    }
}
