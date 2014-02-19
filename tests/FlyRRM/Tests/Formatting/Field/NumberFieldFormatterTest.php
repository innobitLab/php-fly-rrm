<?php
namespace FlyRRM\Tests\Formatting\Field;

use FlyRRM\Formatting\Field\NumberFieldFormatter;

class NumberFieldFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Formatting\Field\NumberFieldFormatter */
    private $formatter;

    protected function setUp()
    {
        $this->formatter = new NumberFieldFormatter();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_a_number_field_formatter_require_a_number()
    {
        $this->formatter->format('hello world');
    }

    public function test_that_null_is_formatted_to_null()
    {
        $this->assertEquals(null, $this->formatter->format(null));
    }

    public function test_that_a_number_is_correctly_formatted()
    {

        $this->assertEquals('1250.53', $this->formatter->format(1250.53));
    }

}
