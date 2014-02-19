<?php
namespace FlyRRM\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\InvalidMappingConfigurationException;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class YamlRelationshipMappingParser
{
    private $externalTypeToInternal = array(
        'one-to-many' => Relationship::TYPE_ONE_TO_MANY,
        'many-to-one' => Relationship::TYPE_MANY_TO_ONE
    );

    public function validateRelationship(array $rawYamlParsedRelationship)
    {
        if (!isset($rawYamlParsedRelationship['type'])) {
            throw new InvalidMappingConfigurationException('missing relationship type key');
        }

        if (!isset($rawYamlParsedRelationship['join-column'])) {
            throw new InvalidMappingConfigurationException('missing relationship join-column key');
        }

        if (empty($rawYamlParsedRelationship['type'])) {
            throw new InvalidMappingConfigurationException('relationship type must be set');
        }

        if (empty($rawYamlParsedRelationship['join-column'])) {
            throw new InvalidMappingConfigurationException('relationship join-column must be set');
        }

        if (!isset($rawYamlParsedRelationship['resource'])) {
            throw new InvalidMappingConfigurationException('missing relationship resource key');
        }

        if (empty($rawYamlParsedRelationship['resource'])) {
            throw new InvalidMappingConfigurationException('relationship resource must be set');
        }

        if (!array_key_exists($rawYamlParsedRelationship['type'], $this->externalTypeToInternal)) {
            throw new InvalidMappingConfigurationException(sprintf('unknown relationship type \'%s\'', $rawYamlParsedRelationship['type']));
        }
    }

    public function parseRelationship(Resource $mainResource, Resource $referencedResource, array $rawYamlParsedRelationship)
    {
        $relationshipType = $this->externalTypeToInternal[$rawYamlParsedRelationship['type']];
        $joinColumn = $rawYamlParsedRelationship['join-column'];

        return new Relationship($mainResource, $referencedResource, $relationshipType, $joinColumn);
    }
}
