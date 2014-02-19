<?php
namespace FlyRRM\Tests\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Parsing\Yaml\YamlMappingParser;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class TestYamlMappingParser extends \PHPUnit_Framework_TestCase
{
    private $yamlParser;

    protected function setUp()
    {
        $this->yamlParser = new YamlMappingParser();
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage configuration content cannot be empty
     */
    public function test_that_parsing_an_empty_config_is_invalid()
    {
        $this->parseInput('');
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage invalid yaml structure
     */
    public function test_that_invalid_yaml_is_invalid()
    {
        $input = <<<EOT
 resource: ''
testme
EOT;

        $this->parseInput($input);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing root resource key
     */
    public function test_that_a_mapping_without_root_resource_key_is_invalid()
    {
        $input = <<<EOT
helloworld: ''
EOT;

        $this->parseInput($input);
    }

    public function test_that_a_resource_mapping_returns_resource_mapping_object()
    {
        $input = <<<EOT
resource:
    alias: 'myResource'
    table: 'resource_table'
    primary-key: 'my_id'

    fields:
        -
            name: 'field_one'
            alias: 'fieldOne'

        -
            name: 'field_two'
            alias: 'fieldTwo'
            type: 'datetime'

        -
            name: 'field_three'
            alias: 'fieldThree'
            type: 'string'

        -
            name: 'field_four'
            alias: 'fieldFour'
            type: 'date'

        -
            name: 'field_five'
            alias: 'fieldFive'
            type: 'number'
EOT;

        /** @var \FlyRRM\Mapping\Resource $rootResource */
        $rootResource = $this->parseInput($input);

        $this->assertInstanceOf('FlyRRM\Mapping\Resource', $rootResource);
        $this->assertEquals('myResource', $rootResource->getAlias());
        $this->assertEquals('resource_table', $rootResource->getTable());
        $this->assertEquals('my_id', $rootResource->getPrimaryKey());
        $this->assertTrue(is_array($rootResource->getFields()));

        $fields = $rootResource->getFields();

        /** @var \FlyRRM\Mapping\Field $firstField */
        $firstField = $fields[0];
        $this->assertInstanceOf('FlyRRM\Mapping\Field', $firstField);
        $this->assertEquals('field_one', $firstField->getName());
        $this->assertEquals('fieldOne', $firstField->getAlias());
        $this->assertEquals('string', $firstField->getType());

        /** @var \FlyRRM\Mapping\Field $secondField */
        $secondField = $fields[1];
        $this->assertInstanceOf('FlyRRM\Mapping\Field', $secondField);
        $this->assertEquals('field_two', $secondField->getName());
        $this->assertEquals('fieldTwo', $secondField->getAlias());
        $this->assertEquals('datetime', $secondField->getType());

        /** @var \FlyRRM\Mapping\Field $thirdField */
        $thirdField = $fields[2];
        $this->assertInstanceOf('FlyRRM\Mapping\Field', $thirdField);
        $this->assertEquals('field_three', $thirdField->getName());
        $this->assertEquals('fieldThree', $thirdField->getAlias());
        $this->assertEquals('string', $thirdField->getType());

        /** @var \FlyRRM\Mapping\Field $fourthField */
        $fourthField = $fields[3];
        $this->assertInstanceOf('FlyRRM\Mapping\Field', $fourthField);
        $this->assertEquals('field_four', $fourthField->getName());
        $this->assertEquals('fieldFour', $fourthField->getAlias());
        $this->assertEquals('date', $fourthField->getType());

        /** @var \FlyRRM\Mapping\Field $fifthField */
        $fifthField = $fields[4];
        $this->assertInstanceOf('FlyRRM\Mapping\Field', $fifthField);
        $this->assertEquals('field_five', $fifthField->getName());
        $this->assertEquals('fieldFive', $fifthField->getAlias());
        $this->assertEquals('number', $fifthField->getType());
    }

    public function test_that_two_resource_have_the_correct_id()
    {
        $input = <<<EOT
resource:
    alias: 'myResource'
    table: 'myTable'
    primary-key: 'my_id'

    fields:
        -
            name: 'myName'
            alias: 'myAlias'

    relationships:
        -
            type: 'many-to-one'
            join-column: 'idMyTable'

            resource:
                alias: 'myOtherResource'
                table: 'myOtherTable'
                primary-key: 'my_other_id'

                fields:
                    -
                        name: 'myOtherField'
                        alias: 'myOtherAlias'
EOT;

        /** @var \FlyRRM\Mapping\Resource $rootResource */
        $rootResource = $this->parseInput($input);
        $relationships = $rootResource->getRelationships();
        /** @var \FlyRRM\Mapping\Relationship $firstRelationship */
        $firstRelationship = $relationships[0];
        $referencedResource = $firstRelationship->getReferencedResource();

        $this->assertEquals('myT_0', $rootResource->getResourceUniqueIdentifier());
        $this->assertEquals('myO_1', $referencedResource->getResourceUniqueIdentifier());
    }

    public function test_that_a_resource_with_one_related_many_to_one_is_mapped()
    {
        $input = <<<EOT
resource:
    alias: 'myResource'
    table: 'myTable'
    primary-key: 'my_id'

    fields:
        -
            name: 'myName'
            alias: 'myAlias'

    relationships:
        -
            type: 'many-to-one'
            join-column: 'idMyTable'

            resource:
                alias: 'myOtherResource'
                table: 'myOtherTable'
                primary-key: 'my_other_id'

                fields:
                    -
                        name: 'myOtherField'
                        alias: 'myOtherAlias'
EOT;

        /** @var \FlyRRM\Mapping\Resource $rootResource */
        $rootResource = $this->parseInput($input);

        $relationships = $rootResource->getRelationships();
        $this->assertEquals(1, sizeof($relationships));

        /** @var \FlyRRM\Mapping\Relationship $firstRelationship */
        $firstRelationship = $relationships[0];

        $expectedMainResource = new Resource('myT_0', 'myResource', 'myTable', 'my_id');
        $expectedMainResourceFields = array(
            new Field($expectedMainResource, 'myAlias', 'myName', Field::TYPE_STRING)
        );
        $expectedMainResource->addFieldsArray($expectedMainResourceFields);

        $expectedReferencedResource = new Resource('myO_1', 'myOtherResource', 'myOtherTable', 'my_other_id');
        $expectedReferencedResourceFields = array(
            new Field($expectedReferencedResource, 'myOtherAlias', 'myOtherField', Field::TYPE_STRING)
        );
        $expectedReferencedResource->addFieldsArray($expectedReferencedResourceFields);

        $expectedRelationship = new Relationship($expectedMainResource, $expectedReferencedResource, 'many-to-one', 'idMyTable');
        $expectedMainResource->addRelationship($expectedRelationship);

        $this->assertEquals($expectedRelationship, $firstRelationship);
    }

    private function parseInput($input)
    {
        return $this->yamlParser->parse($input);
    }
}
