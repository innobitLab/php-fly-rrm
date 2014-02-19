<?php
namespace FlyRRM\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\InvalidMappingConfigurationException;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;
use FlyRRM\Tests\Mapping\Parsing\Yaml\YamlFieldMappingParserTest;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlMappingParser
{
    private $resourceParser;
    private $fieldParser;
    private $relationshipParser;
    private $resourcesCount;

    public function __construct()
    {
        $this->fieldParser = new YamlFieldMappingParser();
        $this->resourceParser = new YamlResourceMappingParser();
        $this->relationshipParser = new YamlRelationshipMappingParser();
        $this->resourcesCount = 0;
    }

    public function parse($input)
    {
        $this->validateInput($input);

        $parsedYaml = $this->parseYaml($input);

        $parsedResource = $this->retrieveRootResource($parsedYaml);
        return $this->convertParsedResourceToObj($parsedResource);
    }

    private function validateInput($input)
    {
        if (empty($input)) {
            throw new InvalidMappingConfigurationException('configuration content cannot be empty');
        }
    }

    private function parseYaml($input)
    {
        try {
            $parsedYaml = Yaml::parse($input);
            return $parsedYaml;
        } catch (ParseException $e) {
            throw new InvalidMappingConfigurationException('invalid yaml structure', null, $e);
        }
    }

    private function retrieveRootResource(array $parsedYaml)
    {
        if (!isset($parsedYaml['resource'])) {
            throw new InvalidMappingConfigurationException('missing root resource key');
        }

        return $parsedYaml['resource'];
    }

    private function convertParsedResourceToObj(array $parsedResource)
    {
        $this->validateCompleteResource($parsedResource);

        $rootResource = $this->resourceParser->parseResource($parsedResource, $this->resourcesCount++);
        $fields = $this->convertParsedFieldsToObjArray($rootResource, $parsedResource['fields']);

        $relationships = null;

        $parsedRelationships = isset($parsedResource['relationships']) ? $parsedResource['relationships'] : null;

        if ($parsedRelationships) {
            foreach ($parsedRelationships as $r) {
                $referencedParsedResource = $r['resource'];
                $referencedResource = $this->convertParsedResourceToObj($referencedParsedResource);

                $relationship = $this->relationshipParser->parseRelationship($rootResource, $referencedResource, $r);

                $rootResource->addRelationship($relationship);
            }
        }

        $rootResource->addFieldsArray($fields);

        return $rootResource;
    }

    private function validateCompleteResource(array $parsedResource)
    {
        $this->resourceParser->validateResource($parsedResource);
        $this->validateResourceFields($parsedResource['fields']);

        if (isset($parsedResource['relationships'])) {
            $this->validateResourceRelationships($parsedResource);
        }
    }

    private function validateResourceFields(array $parsedFields)
    {
        foreach ($parsedFields as $f) {
            $this->fieldParser->validateField($f);
        }
    }

    private function validateResourceRelationships(array $parsedResource)
    {
        foreach ($parsedResource['relationships'] as $r) {
            $this->relationshipParser->validateRelationship($r);
        }
    }

    private function convertParsedFieldsToObjArray(Resource $resource, array $parsedFields)
    {
        $res = array();

        foreach ($parsedFields as $f) {
            $res[] = $this->fieldParser->parseField($resource, $f);
        }

        return $res;
    }
}
