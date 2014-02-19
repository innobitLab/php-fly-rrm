<?php
namespace FlyRRM\Tests\Hydration\Field;

use FlyRRM\Hydration\Field\NumberFieldHydrator;

class NumberFieldHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Hydration\Field\NumberFieldHydrator */
    private $hydrator;

    protected function setUp()
    {
        $this->hydrator = new NumberFieldHydrator();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_an_invalid_number_throws_exception()
    {
        $this->hydrator->hydrate('pluto');
    }

    public function test_that_a_null_value_returns_null()
    {
        $this->assertSame(null, $this->hydrator->hydrate(null));
    }

    public function test_that_a_correct_number_is_hydrated_to_number()
    {
        $originalValue = '1250.23';
        $expectedValue = 1250.23;

        $this->assertSame($expectedValue, $this->hydrator->hydrate($originalValue));
    }
}
