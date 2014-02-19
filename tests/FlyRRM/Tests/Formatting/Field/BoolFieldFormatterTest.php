<?php
namespace FlyRRM\Tests\Formatting\Field;

use FlyRRM\Formatting\Field\BoolFieldFormatter;

class BoolFieldFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_a_bool_field_formatter_require_a_bool()
    {
        $formatter = new BoolFieldFormatter();
        $formatter->format('hello world');
    }

    public function test_that_a_bool_is_correctly_formatted()
    {
        $formatter = new BoolFieldFormatter();
        $this->assertSame(true, $formatter->format(true));
    }

}
