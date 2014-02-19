<?php
namespace FlyRRM\Tests\Formatting\Field;

use FlyRRM\Formatting\Field\DateTimeFieldFormatter;

class DateTimeFieldFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function test_that_a_date_field_formatter_with_format_string_has_default()
    {
        $formatter = new DateTimeFieldFormatter();
        $this->assertEquals('Y-m-d H:i:s', $formatter->getFormatString());
    }

    public function test_that_a_date_field_formatter_can_take_a_custom_format_string()
    {
        $formatter = new DateTimeFieldFormatter('d/m/Y H:i:s');
        $this->assertEquals('d/m/Y H:i:s', $formatter->getFormatString());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_that_a_date_field_formatter_require_a_date()
    {
        $formatter = new DateTimeFieldFormatter();
        $formatter->format('hello world');
    }

    public function test_that_a_null_is_formatted_to_null()
    {
        $formatter = new DateTimeFieldFormatter();
        $this->assertSame(null, $formatter->format(null));
    }

    public function test_that_a_datetime_is_correctly_formatted()
    {
        $formatter = new DateTimeFieldFormatter('d/m/Y H:i:s');
        $date = new \DateTime('2014-02-18 13:54:12');

        $this->assertEquals('18/02/2014 13:54:12', $formatter->format($date));
    }
}
