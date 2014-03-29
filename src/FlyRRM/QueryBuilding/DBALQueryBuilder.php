<?php
namespace FlyRRM\QueryBuilding;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class DBALQueryBuilder implements QueryBuilder
{

    public function buildQuery(Resource $resource)
    {
        return 'select ' . $this->buildSelectFields($resource) .
               ' from ' . $this->buildFromClause($resource) .
               $this->buildWhereClause($resource);
    }

    private function buildSelectFields(Resource $resource)
    {
        $selectFields = $this->buildPrimaryKeySelectFieldClause($resource);
        $fields = $resource->getFields();

        /** @var \FlyRRM\Mapping\Field $f */
        foreach ($fields as $f) {
            $selectFields .= ', ' . $this->buildSelectFieldClause($f);
        }

        if ($resource->hasRelationships()) {
            $relationships = $resource->getRelationships();

            /** @var \FlyRRM\Mapping\Relationship $r */
            foreach ($relationships as $r) {
                if ($r->getType() === Relationship::TYPE_MANY_TO_ONE) {
                    $selectFields .= ', ' . $this->buildSelectFields($r->getReferencedResource());
                }
            }
        }

        return $selectFields;
    }

    private function buildPrimaryKeySelectFieldClause(Resource $resource)
    {
        return $resource->getResourceUniqueIdentifier() . '.' . $resource->getPrimaryKey() . ' as ' . $resource->getResourceUniqueIdentifier() .'_' . $resource->getPrimaryKey();
    }

    private function buildSelectFieldClause(Field $field)
    {
        $resource = $field->getResource();
        return $this->buildFieldUniqueName($field) . ' as ' . $resource->getResourceUniqueIdentifier() . '_' . $field->getAlias();
    }

    private function buildFromClause(Resource $resource)
    {
        return $resource->getTable() . ' ' . $resource->getResourceUniqueIdentifier() . $this->buildFromJoinsClause($resource);
    }

    private function buildFromJoinsClause(Resource $resource)
    {
        $joinsClause = '';

        if ($resource->hasRelationships()) {
            $relationships = $resource->getRelationships();

            /** @var \FlyRRM\Mapping\Relationship $rel */
            foreach ($relationships as $rel) {
                if ($rel->getType() === Relationship::TYPE_MANY_TO_ONE) {
                    $referencedResource = $rel->getReferencedResource();
                    $mainResource = $rel->getMainResource();

                    $joinCondition = $mainResource->getResourceUniqueIdentifier() . '.' . $rel->getJoinColumn() . ' = ' . $referencedResource->getResourceUniqueIdentifier() . '.' .$referencedResource->getPrimaryKey();
                    $joinsClause .= ' left outer join ' . $referencedResource->getTable() . ' ' . $referencedResource->getResourceUniqueIdentifier() . ' on ' . $joinCondition . $this->buildFromJoinsClause($referencedResource);
                }
            }
        }

        return $joinsClause;
    }

    public function buildToManyQueries(Relationship $relationship)
    {
        if ($relationship->getType() !== Relationship::TYPE_ONE_TO_MANY) {
            throw new \InvalidArgumentException('relationship must be of type one-to-many');
        }

        return $this->buildQuery($relationship->getReferencedResource()) . $this->buildToManyWhereClause($relationship);
    }

    private function buildToManyWhereClause(Relationship $rel)
    {
        $mainResource = $rel->getMainResource();
        $referencedResource = $rel->getReferencedResource();
        return ' where ' . $referencedResource->getResourceUniqueIdentifier() . '.' . $rel->getJoinColumn() . ' = ' . ':' . $mainResource->getResourceUniqueIdentifier() . '_' . $mainResource->getPrimaryKey();
    }

    private function buildWhereClause(Resource $resource)
    {
        $whereClause = $resource->getWhereClause();
        $whereClause = (empty($whereClause) ? '' : ' where ' . $whereClause);

        /** @var $f \FlyRRM\Mapping\Field */
        foreach ($resource->getFields() as $f) {
            $fieldCompleteAlias = $resource->getAlias() . '.' . $f->getAlias();
            $fieldCompleteName = $this->buildFieldUniqueName($f);
            $whereClause = str_replace($fieldCompleteAlias, $fieldCompleteName, $whereClause);
        }

        return $whereClause;
    }

    private function buildFieldUniqueName(Field $field)
    {
        $resource = $field->getResource();
        return $resource->getResourceUniqueIdentifier() . '.' . $field->getName();
    }
}
