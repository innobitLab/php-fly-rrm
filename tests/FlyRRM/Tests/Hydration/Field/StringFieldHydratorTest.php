<?php
namespace FlyRRM\Tests\Hydration\Field;

use FlyRRM\Hydration\Field\StringFieldHydrator;

class StringFieldHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Hydration\Field\StringFieldHydrator */
    private $hydrator;

    protected function setUp()
    {
        $this->hydrator = new StringFieldHydrator();
    }

    public function test_that_a_null_string_is_hydrated_to_null()
    {
        $this->assertSame(null, $this->hydrator->hydrate(null));
    }

    public function test_that_a_string_is_hydrated_to_string()
    {
        $this->assertSame('hello world!', $this->hydrator->hydrate('hello world!'));
    }

    public function test_that_a_numeric_value_is_hydrated_to_string()
    {
        $this->assertSame('1250.23', $this->hydrator->hydrate(1250.23));
    }
}
