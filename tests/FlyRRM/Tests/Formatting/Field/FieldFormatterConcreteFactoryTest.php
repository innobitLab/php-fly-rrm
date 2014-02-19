<?php
namespace FlyRRM\Tests\Formatting\Field;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Resource;
use FlyRRM\Formatting\Field\FieldFormatterAbstractFactory;
use FlyRRM\Formatting\Field\FieldFormatterConcreteFactory;

class FieldFormatterConcreteFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldFormatterAbstractFactory $factory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new FieldFormatterConcreteFactory();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_an_unknown_field_type_throws_exception()
    {
        $testField = $this->generateTestField('GigoWatt');
        $this->factory->buildFieldFormatterForField($testField);
    }

    public function test_that_a_string_field_get_string_formatter()
    {
        $testField = $this->generateTestField(Field::TYPE_STRING);
        $this->assertInstanceOf('FlyRRM\Formatting\Field\StringFieldFormatter', $this->factory->buildFieldFormatterForField($testField));
    }

    public function test_that_a_datetime_field_get_datetime_formatter()
    {
        $testField = $this->generateTestField(Field::TYPE_DATETIME);
        $this->assertInstanceOf('FlyRRM\Formatting\Field\DateTimeFieldFormatter', $this->factory->buildFieldFormatterForField($testField));
    }

    public function test_that_a_date_field_get_date_formatter()
    {
        $testField = $this->generateTestField(Field::TYPE_DATE);
        $formatter = $this->factory->buildFieldFormatterForField($testField);
        $this->assertInstanceOf('FlyRRM\Formatting\Field\DateTimeFieldFormatter', $formatter);
        $this->assertEquals('Y-m-d', $formatter->getFormatString());
    }

    public function test_that_a_number_field_get_number_formatter()
    {
        $testField = $this->generateTestField(Field::TYPE_NUMBER);
        $this->assertInstanceOf('FlyRRM\Formatting\Field\NumberFieldFormatter', $this->factory->buildFieldFormatterForField($testField));
    }

    public function test_that_a_bool_field_get_bool_formatter()
    {
        $testField = $this->generateTestField(Field::TYPE_BOOL);
        $this->assertInstanceOf('FlyRRM\Formatting\Field\BoolFieldFormatter', $this->factory->buildFieldFormatterForField($testField));
    }

    private function generateTestField($fieldType)
    {
        $testResource = new Resource('my__0', 'myResource', 'my_resource', 'my_id');
        $testField = new Field($testResource, 'myField', 'my_field', $fieldType);
        return $testField;
    }
}
