<?php
namespace FlyRRM\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\InvalidMappingConfigurationException;
use FlyRRM\Mapping\Resource;

class YamlFieldMappingParser
{
    private $externalTypeToInternalType = array(
        '' => Field::TYPE_STRING,
        'string' => Field::TYPE_STRING,
        'datetime' => Field::TYPE_DATETIME,
        'date' => Field::TYPE_DATE,
        'number' => Field::TYPE_NUMBER,
        'bool' => Field::TYPE_BOOL
    );

    public function parseField(Resource $resource, array $rawYamlParsedField)
    {
        return $this->convertSingleParsedFieldToObj($resource, $rawYamlParsedField);
    }

    public function validateField($rawYamlParsedField)
    {
        if (!isset($rawYamlParsedField['name'])) {
            throw new InvalidMappingConfigurationException('missing field name key');
        }

        if (empty($rawYamlParsedField['name'])) {
            throw new InvalidMappingConfigurationException('field name must be set');
        }

        if (!isset($rawYamlParsedField['alias'])) {
            throw new InvalidMappingConfigurationException('missing field alias key');
        }

        if (empty($rawYamlParsedField['alias'])) {
            throw new InvalidMappingConfigurationException('field alias must be set');
        }

        $parsedFieldType = $this->parseFieldType($rawYamlParsedField);

        if (!array_key_exists($parsedFieldType, $this->externalTypeToInternalType)) {
            throw new InvalidMappingConfigurationException(sprintf('unknown field type \'%s\'', $parsedFieldType));
        }
    }

    private function parseFieldType($rawYamlParsedField)
    {
        return isset($rawYamlParsedField['type']) ? $rawYamlParsedField['type'] : '';
    }

    private function convertSingleParsedFieldToObj(Resource $resource, array $rawYamlParsedField)
    {
        $parsedFieldType = isset($rawYamlParsedField['type']) ? $rawYamlParsedField['type'] : '';
        $internalFieldType = $this->externalTypeToInternalType[$parsedFieldType];
        $formatString = isset($rawYamlParsedField['format-string']) ? $rawYamlParsedField['format-string'] : null;

        return new Field($resource, $rawYamlParsedField['alias'], $rawYamlParsedField['name'], $internalFieldType, $formatString);
    }
}
