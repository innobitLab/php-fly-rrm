<?php
namespace FlyRRM\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\InvalidMappingConfigurationException;
use FlyRRM\Mapping\Resource;

class YamlResourceMappingParser
{

    public function validateResource(array $rawYamlParsedResource)
    {
        if (!isset($rawYamlParsedResource['alias'])) {
            throw new InvalidMappingConfigurationException('missing resource alias key');
        }

        if (empty($rawYamlParsedResource['alias'])) {
            throw new InvalidMappingConfigurationException('resource alias must be set');
        }

        if (!isset($rawYamlParsedResource['table'])) {
            throw new InvalidMappingConfigurationException('missing resource table key');
        }

        if (empty($rawYamlParsedResource['table'])) {
            throw new InvalidMappingConfigurationException('resource table must be set');
        }

        if (!isset($rawYamlParsedResource['primary-key'])) {
            throw new InvalidMappingConfigurationException('missing resource primary-key key');
        }

        if (empty($rawYamlParsedResource['primary-key'])) {
            throw new InvalidMappingConfigurationException('resource primary-key must be set');
        }

        if (!isset($rawYamlParsedResource['fields'])) {
            throw new InvalidMappingConfigurationException('missing resource fields key');
        }

        if (!is_array($rawYamlParsedResource['fields'])) {
            throw new InvalidMappingConfigurationException('resource fields must be a sequence');
        }

        if (empty($rawYamlParsedResource['fields'])) {
            throw new InvalidMappingConfigurationException('resource fields cannot be empty');
        }
    }

    public function parseResource(array $rawYamlParsedResource, $currentId)
    {
        $table = $rawYamlParsedResource['table'];
        $id = $this->generateResourceUniqueIdentifier($currentId, $table);

        $resource = new Resource(
            $id,
            $rawYamlParsedResource['alias'],
            $table,
            $rawYamlParsedResource['primary-key']);

        if (isset($rawYamlParsedResource['where']))
            $resource->setWhereClause($rawYamlParsedResource['where']);

        return $resource;
    }

    private function generateResourceUniqueIdentifier($currentId, $table)
    {
        $id = substr($table, 0, 3);
        $id .= '_' . $currentId;
        return $id;
    }
}
