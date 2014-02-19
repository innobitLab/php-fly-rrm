<?php
namespace FlyRRM\Tests\Formatting\Field;

use FlyRRM\Formatting\Field\NumberFieldFormatter;

class NumberFieldFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_a_number_field_formatter_require_a_number()
    {
        $formatter = new NumberFieldFormatter();
        $formatter->format('hello world');
    }

    public function test_that_a_number_is_correctly_formatted()
    {
        $formatter = new NumberFieldFormatter();
        $this->assertEquals('1250.53', $formatter->format(1250.53));
    }

}
