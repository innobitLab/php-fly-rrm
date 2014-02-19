<?php
namespace FlyRRM\Tests\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\Parsing\Yaml\YamlRelationshipMappingParser;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class YamlRelationshipMappingParserTest extends \PHPUnit_Framework_TestCase
{

    /** @var \FlyRRM\Mapping\Parsing\Yaml\YamlRelationshipMappingParser */
    private $parser;

    protected function setUp()
    {
        $this->parser = new YamlRelationshipMappingParser();
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing relationship type key
     */
    public function test_that_a_relationship_without_type_key_is_invalid()
    {
        $rawYamlParsedRelationship = array('join-column' => 'idMyTable');
        $this->parser->validateRelationship($rawYamlParsedRelationship);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing relationship join-column key
     */
    public function test_that_a_relationship_without_join_clause_key_is_invalid()
    {
        $rawYamlParsedRelationship = array('type' => 'many-to-one');
        $this->parser->validateRelationship($rawYamlParsedRelationship);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage relationship type must be set
     */
    public function test_that_a_relationship_with_empty_type_is_invalid()
    {
        $rawYamlParsedRelationship = array('type' => '', 'join-column' => 'idMyTable');
        $this->parser->validateRelationship($rawYamlParsedRelationship);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage relationship join-column must be set
     */
    public function test_that_a_relationship_with_empty_join_clause_is_invalid()
    {
        $rawYamlParsedRelationship = array('type' => 'many-to-one', 'join-column' => '');
        $this->parser->validateRelationship($rawYamlParsedRelationship);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing relationship resource key
     */
    public function test_that_a_relationship_without_resource_key_is_invalid()
    {
        $rawYamlParsedRelationship = array('type' => 'many-to-one', 'join-column' => 'idMyTable');
        $this->parser->validateRelationship($rawYamlParsedRelationship);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage relationship resource must be set
     */
    public function test_that_a_relationship_with_empty_resource_is_invalid()
    {
        $rawYamlParsedRelationship = array('type' => 'many-to-one', 'join-column' => 'idMyTable', 'resource' => '');
        $this->parser->validateRelationship($rawYamlParsedRelationship);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage unknown relationship type 'some-to-some'
     */
    public function test_that_an_unknown_relationship_type_is_invalid()
    {
        $rawYamlParsedRelationship = array('type' => 'some-to-some', 'join-column' => 'idMyTable', 'resource' => 'other res');
        $this->parser->validateRelationship($rawYamlParsedRelationship);
    }

    public function test_that_a_correct_many_to_one_relationship_is_parsed()
    {
        $mainResource = new Resource('my__0', 'myAlias', 'my_table', 'my_id');
        $referencedResource = new Resource('my__1', 'myOtherAlias', 'my_other_table', 'my_other_id');
        $rawYamlParsedResource = array('type' => 'many-to-one',
            'join-column' => 'idMyTable',
            'resource' => 'resource data');

        $relationship = $this->parser->parseRelationship($mainResource, $referencedResource, $rawYamlParsedResource);

        $this->assertEquals(Relationship::TYPE_MANY_TO_ONE, $relationship->getType());
        $this->assertEquals('idMyTable', $relationship->getJoinColumn());
    }

    public function test_that_a_correct_one_to_many_relationship_is_parsed()
    {
        $mainResource = new Resource('my__0', 'myAlias', 'my_table', 'my_id');
        $referencedResource = new Resource('my__1', 'myOtherAlias', 'my_other_table', 'my_other_id');
        $rawYamlParsedResource = array('type' => 'one-to-many',
            'join-column' => 'idMyTable',
            'resource' => 'resource data');

        $relationship = $this->parser->parseRelationship($mainResource, $referencedResource, $rawYamlParsedResource);

        $this->assertEquals(Relationship::TYPE_ONE_TO_MANY, $relationship->getType());
        $this->assertEquals('idMyTable', $relationship->getJoinColumn());
    }
}
