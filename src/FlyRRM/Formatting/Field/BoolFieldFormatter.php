<?php
namespace FlyRRM\Formatting\Field;

class BoolFieldFormatter
{
    public function format($rawValue)
    {
        if (!is_bool($rawValue)) {
            throw new \InvalidArgumentException();
        }

        return $rawValue;
    }
}
