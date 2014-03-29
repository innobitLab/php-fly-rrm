<?php
namespace FlyRRM\Tests\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\Parsing\Yaml\YamlResourceMappingParser;

class YamlResourceMappingParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Mapping\Parsing\Yaml\YamlResourceMappingParser */
    private $parser;

    protected function setUp()
    {
        $this->parser = new YamlResourceMappingParser();
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing resource alias key
     */
    public function test_that_a_resource_without_resource_alias_key_is_invalid()
    {
        $rawYamlParsedResource = array('table' => 'myTable');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage resource alias must be set
     */
    public function test_that_a_resource_without_alias_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => '');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing resource table key
     */
    public function test_that_a_resource_without_resource_table_key_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => 'myResource');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage resource table must be set
     */
    public function test_that_a_resource_without_table_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => 'myResource', 'table' => '');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing resource primary-key key
     */
    public function test_that_a_resource_without_primary_key_key_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => 'myResource', 'table' => 'myTable');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage resource primary-key must be set
     */
    public function test_that_a_resource_without_primary_key_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => 'myResource', 'table' => 'myTable', 'primary-key' => '');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing resource fields key
     */
    public function test_that_a_resource_without_resource_fields_key_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => 'myResource', 'table' => 'myTable', 'primary-key' => 'my_id');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage resource fields must be a sequence
     */
    public function test_that_a_resource_where_resource_fields_is_not_a_sequence_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => 'myResource', 'table' => 'myTable', 'primary-key' => 'my_id', 'fields' => 'hello world');
        $this->parser->validateResource($rawYamlParsedResource);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage resource fields cannot be empty
     */
    public function test_that_a_resource_where_resource_fields_is_empty_is_invalid()
    {
        $rawYamlParsedResource = array('alias' => 'myResource', 'table' => 'myTable', 'primary-key' => 'my_id', 'fields' => array());
        $this->parser->validateResource($rawYamlParsedResource);
    }

    public function test_that_a_correct_resource_is_parsed()
    {
        $rawYamlParsedResource = array('alias' => 'myResource', 'table' => 'myTable', 'primary-key' => 'my_id', 'fields' => array('field'));
        $resource = $this->parser->parseResource($rawYamlParsedResource, 1);

        $this->assertEquals('myResource', $resource->getAlias());
        $this->assertEquals('myTable', $resource->getTable());
        $this->assertEquals('my_id', $resource->getPrimaryKey());
        $this->assertEquals('myT_1', $resource->getResourceUniqueIdentifier());
    }

    public function test_that_a_resource_could_have_a_where_clause()
    {
        $rawYamlParsedResource = array(
            'alias' => 'myResource',
            'table' => 'myTable',
            'primary-key' => 'my_id',
            'fields' => array('field'),
            'where' => 'my_id = 3');

        $resource = $this->parser->parseResource($rawYamlParsedResource, 1);

        $this->assertEquals('my_id = 3', $resource->getWhereClause());
    }
}
