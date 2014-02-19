<?php
namespace FlyRRM\Hydration;

use FlyRRM\Hydration\Field\DateTimeFieldHydrator;
use FlyRRM\Hydration\Field\FieldHydrationAbstractFactory;
use FlyRRM\Hydration\Field\FieldHydrationConcreteFactory;
use FlyRRM\Hydration\Field\StringFieldHydrator;
use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class ArrayHydrator
{
    private $fieldHydrationFactory;

    public function __construct(FieldHydrationAbstractFactory $fieldHydrationFactory)
    {
        $this->fieldHydrationFactory = $fieldHydrationFactory;
    }

    public function hydrate(array $plainData, Resource $resource)
    {
        $res = array();

        foreach ($plainData as $row) {
            $rowRes =  $this->hydrateResourceFields($resource, $row);
            $rowRes = array_merge($rowRes, $this->hydrateResourceRelationships($resource, $row));
            $res[] = $rowRes;
        }

        return array($resource->getAlias() => $res);
    }

    private function hydrateResourceFields(Resource $resource, array $row)
    {
        $allNull = true;
        $res = array();

        /** @var \FlyRRM\Mapping\Field $f */
        foreach ($resource->getFields() as $f) {
            $rawValue = $row[$resource->getResourceUniqueIdentifier() . '_' . $f->getAlias()];

            $hydrator = $this->fieldHydrationFactory->buildFieldHydratorForField($f);
            $res[$f->getAlias()] = $hydrator->hydrate($rawValue);

            if ($rawValue !== null) {
                $allNull = false;
            }
        }

        // TODO
        // GT: at the moment we don't know what's the referenced resource id, so we check if all fields are null
        // in order to assert that the referenced resource is null.
        if ($allNull) {
            return null;
        }

        return $res;
    }

    private function hydrateResourceRelationships(Resource $resource, $row)
    {
        $rowRes = array();

        /** @var \FlyRRM\Mapping\Relationship $r */
        foreach ($resource->getRelationships() as $r) {
            $currentReferencedResource = $r->getReferencedResource();

            if ($r->getType() === Relationship::TYPE_MANY_TO_ONE) {
                $rowRes[$currentReferencedResource->getAlias()] =
                    $this->hydrateResourceFields($currentReferencedResource, $row);

                if ($currentReferencedResource->hasRelationships()) {
                    $referencedsRes = $this->hydrateResourceRelationships($currentReferencedResource, $row);

                    foreach ($referencedsRes as $refResKey => $refResValue) {
                        $rowRes[$currentReferencedResource->getAlias()][$refResKey] = $refResValue;
                    }
                }
            }

            if ($r->getType() === Relationship::TYPE_ONE_TO_MANY) {
                $rowRes = array_merge($rowRes, $this->hydrateOneToManySubResource($currentReferencedResource, $row));
            }
        }

        return $rowRes;
    }

    private function hydrateOneToManySubResource(Resource $referencedResource, array $row)
    {
        $relationshipRows = $row[$referencedResource->getResourceUniqueIdentifier()];
        return $this->hydrate($relationshipRows, $referencedResource);
    }
}
