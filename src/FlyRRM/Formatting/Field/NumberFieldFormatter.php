<?php
namespace FlyRRM\Formatting\Field;

class NumberFieldFormatter
{
    public function format($rawValue)
    {
        if (!is_numeric($rawValue)) {
            throw new \InvalidArgumentException();
        }

        return $rawValue;
    }
}
