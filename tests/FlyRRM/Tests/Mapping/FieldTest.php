<?php
namespace FlyRRM\Tests\Mapping;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Resource;

class FieldTest extends \PHPUnit_Framework_TestCase
{

    public function test_field_construction()
    {
        $resource = $this->buildFakeResource();
        $field = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_DATE, 'd-M-Y');

        $this->assertEquals($resource, $field->getResource());
        $this->assertEquals('myCoolField', $field->getAlias());
        $this->assertEquals('my_cool_field', $field->getName());
        $this->assertEquals(Field::TYPE_DATE, $field->getType());
        $this->assertEquals('d-M-Y', $field->getFormatString());
    }

    public function test_that_a_field_to_string_returns_alias()
    {
        $field = new Field($this->buildFakeResource(), 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $this->assertEquals('myCoolField', $field->__toString());
    }

    private function buildFakeResource()
    {
        return new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
    }

}
