<?php
namespace FlyRRM\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\InvalidMappingConfigurationException;
use FlyRRM\Mapping\Resource;

class YamlFieldMappingParser
{
    const NAME_KEY = 'name';
    const ALIAS_KEY = 'alias';
    const TYPE_KEY = 'type';
    const FORMAT_STRING_KEY = 'format-string';

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
        $this->validateName($rawYamlParsedField);
        $this->validateAlias($rawYamlParsedField);
        $this->validateType($rawYamlParsedField);
    }

    private function validateName(array $rawYamlParsedField)
    {
        if (!array_key_exists(self::NAME_KEY, $rawYamlParsedField)) {
            throw new InvalidMappingConfigurationException('missing field name key');
        }

        if (empty($rawYamlParsedField[self::NAME_KEY])) {
            throw new InvalidMappingConfigurationException('field name must be set');
        }
    }

    private function validateAlias(array $rawYamlParsedField)
    {
        if (array_key_exists(self::ALIAS_KEY, $rawYamlParsedField) && empty($rawYamlParsedField[self::ALIAS_KEY])) {
            throw new InvalidMappingConfigurationException('field alias must be set');
        }
    }

    private function validateType(array $rawYamlParsedField)
    {
        $parsedFieldType = $this->parseFieldType($rawYamlParsedField);

        if (!array_key_exists($parsedFieldType, $this->externalTypeToInternalType)) {
            throw new InvalidMappingConfigurationException(sprintf('unknown field type \'%s\'', $parsedFieldType));
        }
    }

    private function parseFieldType($rawYamlParsedField)
    {
        return isset($rawYamlParsedField[self::TYPE_KEY]) ? $rawYamlParsedField[self::TYPE_KEY] : '';
    }

    private function convertSingleParsedFieldToObj(Resource $resource, array $rawYamlParsedField)
    {
        $parsedFieldType = $this->parseFieldType($rawYamlParsedField);
        $internalFieldType = $this->externalTypeToInternalType[$parsedFieldType];

        $formatString = isset($rawYamlParsedField[self::FORMAT_STRING_KEY]) ? $rawYamlParsedField[self::FORMAT_STRING_KEY] : null;

        $name = $rawYamlParsedField[self::NAME_KEY];
        $alias = array_key_exists(self::ALIAS_KEY, $rawYamlParsedField) ? $rawYamlParsedField[self::ALIAS_KEY] : $name;

        return new Field($resource, $alias, $name, $internalFieldType, $formatString);
    }
}
