<?php
namespace FlyRRM\Tests\Mapping\Parsing\Yaml;

use FlyRRM\Mapping\Parsing\Yaml\YamlFieldMappingParser;
use FlyRRM\Mapping\Resource;

class YamlFieldMappingParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Mapping\Parsing\Yaml\YamlFieldMappingParser */
    private $parser;
    private $buildTestResource;

    protected function setUp()
    {
        $this->parser = new YamlFieldMappingParser();
        $this->buildTestResource = $this->buildTestResource();
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing field name key
     */
    public function test_that_a_field_mapping_without_name_key_is_invalid()
    {
        $rawYamlParsedField = array('hello' => 'world');
        $this->parser->validateField($rawYamlParsedField);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage field name must be set
     */
    public function test_that_a_field_mapping_without_name_is_invalid()
    {
        $rawYamlParsedField = array('name' => '');
        $this->parser->validateField($rawYamlParsedField);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage missing field alias key
     */
    public function test_that_a_field_mapping_without_alias_key_is_invalid()
    {
        $rawYamlParsedField = array('name' => 'myName');
        $this->parser->validateField($rawYamlParsedField);
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage field alias must be set
     */
    public function test_that_a_field_mapping_without_alias_is_invalid()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => '');
        $this->parser->validateField($rawYamlParsedField);
    }

    public function test_that_a_field_mapping_without_type_is_a_string_field()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias');
        $field = $this->parser->parseField($this->buildTestResource, $rawYamlParsedField);

        $this->assertEquals('string', $field->getType());
    }

    /**
     * @expectedException \FlyRRM\Mapping\InvalidMappingConfigurationException
     * @expectedExceptionMessage unknown field type 'GigoWatt'
     */
    public function test_that_a_field_mapping_with_unknown_type_is_invalid()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias', 'type' => 'GigoWatt');
        $this->parser->validateField($rawYamlParsedField);
    }

    public function test_that_a_field_mapping_keep_a_reference_to_his_resource()
    {
        $testResource = $this->buildTestResource;
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias');
        $field = $this->parser->parseField($testResource, $rawYamlParsedField);

        $this->assertEquals($testResource, $field->getResource());
    }

    public function test_that_a_field_mapping_with_type_datetime_is_datetime()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias', 'type' => 'datetime');
        $field = $this->parser->parseField($this->buildTestResource, $rawYamlParsedField);

        $this->assertEquals('datetime', $field->getType());
    }

    public function test_that_a_field_mapping_with_type_date_is_date()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias', 'type' => 'date');
        $field = $this->parser->parseField($this->buildTestResource, $rawYamlParsedField);

        $this->assertEquals('date', $field->getType());
    }

    public function test_that_a_field_mapping_with_type_number_is_number()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias', 'type' => 'number');
        $field = $this->parser->parseField($this->buildTestResource, $rawYamlParsedField);

        $this->assertEquals('number', $field->getType());
        $this->assertEquals(null, $field->getFormatString());
    }

    public function test_that_a_field_mapping_with_type_bool_is_bool()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias', 'type' => 'bool');
        $field = $this->parser->parseField($this->buildTestResource, $rawYamlParsedField);

        $this->assertEquals('bool', $field->getType());
        $this->assertEquals(null, $field->getFormatString());
    }

    public function test_that_a_field_mapping_with_format_string_is_parsed()
    {
        $rawYamlParsedField = array('name' => 'myName', 'alias' => 'myAlias', 'type' => 'number', 'format-string' => '0.##');
        $field = $this->parser->parseField($this->buildTestResource, $rawYamlParsedField);

        $this->assertEquals('0.##', $field->getFormatString());
    }

    private function buildTestResource()
    {
        $testResource = new Resource('my__0', 'myAlias', 'my_table', 'my_id');
        return $testResource;
    }
}
