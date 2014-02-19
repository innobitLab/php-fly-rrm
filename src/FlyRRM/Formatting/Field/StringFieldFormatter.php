<?php
namespace FlyRRM\Formatting\Field;

class StringFieldFormatter
{
    public function format($rawValue)
    {
        return (string)$rawValue;
    }
}
