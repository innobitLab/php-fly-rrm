<?php
namespace FlyRRM\Formatting;

use FlyRRM\Formatting\Field\FieldFormatterAbstractFactory;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class ArrayFormatter
{
    /** @var \FlyRRM\Formatting\Field\FieldFormatterAbstractFactory */
    private $fieldFormatterFactory;

    public function __construct(FieldFormatterAbstractFactory $factory)
    {
        $this->fieldFormatterFactory = $factory;
    }

    public function format(array $structuredData, Resource $resource)
    {
        $resourceRows = $structuredData[$resource->getAlias()];

        $res = array();
        $res[$resource->getAlias()] = $this->formatResource($resource, $resourceRows);

        return $res;
    }

    private function formatResource(Resource $resource, $resourceRows)
    {
        $res = array();

        foreach ($resourceRows as $rowIdx => $row) {
            $res[$rowIdx] = $this->formatResourceRow($resource, $row);
        }

        return $res;
    }

    private function formatResourceRow(Resource $resource, array $row)
    {
        $res = array();

        foreach ($row as $fieldAlias => $fieldValue) {
            $res[$fieldAlias] = $this->formatResourceField($resource, $fieldAlias, $fieldValue);
        }

        return $res;
    }

    public function formatResourceField(Resource $resource, $fieldAlias, $fieldValue) {
        if ($fieldValue === null) {
            return null;
        }

        if (is_array($fieldValue)) {
            $relationshipObj = $resource->getRelationshipByReferencedResourceAlias($fieldAlias);
            return $this->formatRelationship($relationshipObj, $fieldValue);
        }

        return $this->formatSimpleFieldInResource($resource, $fieldAlias, $fieldValue);
    }

    public function formatRelationship(Relationship $relationshipObj, array $value)
    {
        $referencedResource = $relationshipObj->getReferencedResource();

        if ($relationshipObj->getType() === Relationship::TYPE_MANY_TO_ONE) {
            return $this->formatResourceRow($referencedResource, $value);
        }

        if ($relationshipObj->getType() === Relationship::TYPE_ONE_TO_MANY) {
            return $this->formatOneToManyRelationship($referencedResource, $value);
        }
    }

    private function formatOneToManyRelationship(Resource $referencedResource, array $value)
    {
        $subResourceRes = array();

        foreach ($value as $idx => $val) {
            $subResourceRes[$idx] = $this->formatResourceRow($referencedResource, $val);
        }

        return $subResourceRes;
    }

    public function formatSimpleFieldInResource(Resource $resource, $fieldAlias, $fieldValue)
    {
        $fieldObj = $resource->getFieldByAlias($fieldAlias);
        $fieldFormatter = $this->fieldFormatterFactory->buildFieldFormatterForField($fieldObj);
        return $fieldFormatter->format($fieldValue);
    }
}
