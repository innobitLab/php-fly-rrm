<?php
namespace FlyRRM\Mapping;

class Relationship
{
    const TYPE_MANY_TO_ONE = 'many-to-one';
    const TYPE_ONE_TO_MANY = 'one-to-many';

    private $mainResource;
    private $referencedResource;
    private $type;
    private $joinColumn;

    public function getMainResource()
    {
        return $this->mainResource;
    }

    public function getReferencedResource()
    {
        return $this->referencedResource;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getJoinColumn()
    {
        return $this->joinColumn;
    }

    public function __construct(Resource $mainResource, Resource $referencedResource, $type, $joinColumn)
    {
        $this->mainResource = $mainResource;
        $this->referencedResource = $referencedResource;

        $internalType = null;
        switch($type) {
            case 'many-to-one':
                $internalType = self::TYPE_MANY_TO_ONE;
                break;

            case 'one-to-many':
                $internalType = self::TYPE_ONE_TO_MANY;
                break;
        }

        $this->type = $internalType;
        $this->joinColumn = $joinColumn;
    }
}
