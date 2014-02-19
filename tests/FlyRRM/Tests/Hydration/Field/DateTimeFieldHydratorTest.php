<?php
namespace FlyRRM\Tests\Hydration\Field;

use FlyRRM\Hydration\Field\DateTimeFieldHydrator;

class DateTimeFieldHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Hydration\Field\DateTimeFieldHydrator */
    private $hydrator;

    protected function setUp()
    {
        $this->hydrator = new DateTimeFieldHydrator();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_an_invalid_datetime_throws_exception()
    {
        $this->hydrator->hydrate('pluto');
    }

    public function test_that_a_null_datetime_returns_null()
    {
        $this->assertSame(null, $this->hydrator->hydrate(null));
    }

    public function test_that_a_correct_datetime_is_hydrated_to_datetime_object()
    {
        $originalValue = '1987-04-07 06:30:12';
        $expectedValue = new \DateTime('1987-04-07 06:30:12');

        $this->assertEquals($expectedValue, $this->hydrator->hydrate($originalValue));
    }
}
