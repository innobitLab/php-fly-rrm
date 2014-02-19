<?php
namespace FlyRRM\Tests\Hydration\Field;

use FlyRRM\Hydration\Field\BoolFieldHydrator;

class BoolFieldHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Hydration\Field\BoolFieldHydrator  */
    private $hydrator;

    protected function setUp()
    {
        $this->hydrator = new BoolFieldHydrator();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_an_invalid_bool_throws_exception()
    {
        $this->hydrator->hydrate('pluto');
    }

    public function test_that_a_null_bool_returns_null()
    {
        $this->assertSame(null, $this->hydrator->hydrate(null));
    }

    public function test_that_a_one_or_zero_bool_is_hydrated_to_bool()
    {
        $this->assertTrue($this->hydrator->hydrate(1));
        $this->assertFalse($this->hydrator->hydrate(0));
        $this->assertTrue($this->hydrator->hydrate('1'));
        $this->assertFalse($this->hydrator->hydrate('0'));
    }

    public function test_that_a_correct_bool_is_hydrated_to_bool()
    {
        $this->assertTrue($this->hydrator->hydrate('true'));
        $this->assertTrue($this->hydrator->hydrate('false'));
    }
}
