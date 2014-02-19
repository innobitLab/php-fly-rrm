<?php
namespace FlyRRM\Tests\Formatter\Field;

use FlyRRM\Formatting\Field\StringFieldFormatter;

class StringFieldFormatterTest extends \PHPUnit_Framework_TestCase
{

    public function test_that_a_string_is_correctly_formatted()
    {
        $formatter = new StringFieldFormatter();
        $this->assertEquals('hello world!', $formatter->format('hello world!'));
    }

}
