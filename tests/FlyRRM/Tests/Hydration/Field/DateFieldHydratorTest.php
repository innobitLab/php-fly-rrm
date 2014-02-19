<?php
namespace FlyRRM\Tests\Hydration\Field;

use FlyRRM\Hydration\Field\DateFieldHydrator;

class DateFieldHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Hydration\Field\DateFieldHydrator  */
    private $hydrator;

    protected function setUp()
    {
        $this->hydrator = new DateFieldHydrator();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_an_invalid_date_throws_exception()
    {
        $this->hydrator->hydrate('pluto');
    }

    public function test_that_a_null_date_returns_null()
    {
        $this->assertSame(null, $this->hydrator->hydrate(null));
    }

    public function test_that_a_correct_datetime_is_hydrated_to_datetime_object()
    {
        $originalValue = '1987-04-07';
        $expectedValue = new \DateTime('1987-04-07');

        $this->assertEquals($expectedValue, $this->hydrator->hydrate($originalValue));
    }
}
