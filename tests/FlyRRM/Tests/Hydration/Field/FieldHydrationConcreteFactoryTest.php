<?php
namespace FlyRRM\Tests\Hydration\Field;

use FlyRRM\Hydration\Field\FieldHydrationConcreteFactory;
use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Resource;

class FieldHydrationConcreteFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Hydration\Field\FieldHydrationConcreteFactory $factory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new FieldHydrationConcreteFactory();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_an_unknown_field_type_throws_exception()
    {
        $testField = $this->generateTestField('GigoWatt');
        $this->factory->buildFieldHydratorForField($testField);
    }

    public function test_that_a_string_field_get_string_hydrator()
    {
        $testField = $this->generateTestField(Field::TYPE_STRING);
        $this->assertInstanceOf('FlyRRM\Hydration\Field\StringFieldHydrator', $this->factory->buildFieldHydratorForField($testField));
    }

    public function test_that_a_datetime_field_get_datetime_hydrator()
    {
        $testField = $this->generateTestField(Field::TYPE_DATETIME);
        $this->assertInstanceOf('FlyRRM\Hydration\Field\DateTimeFieldHydrator', $this->factory->buildFieldHydratorForField($testField));
    }

    public function test_that_a_datetime_field_get_date_hydrator()
    {
        $testField = $this->generateTestField(Field::TYPE_DATE);
        $this->assertInstanceOf('FlyRRM\Hydration\Field\DateFieldHydrator', $this->factory->buildFieldHydratorForField($testField));
    }

    public function test_that_a_number_field_get_number_hydrator()
    {
        $testField = $this->generateTestField(Field::TYPE_NUMBER);
        $this->assertInstanceOf('FlyRRM\Hydration\Field\NumberFieldHydrator', $this->factory->buildFieldHydratorForField($testField));
    }

    private function generateTestField($fieldType)
    {
        $testResource = new Resource('my__0', 'myResource', 'my_resource', 'my_id');
        $testField = new Field($testResource, 'myField', 'my_field', $fieldType);
        return $testField;
    }
}
