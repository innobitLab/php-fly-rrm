<?php
namespace FlyRRM\Hydration\Field;

class NumberFieldHydrator implements FieldHydrator
{
    public function hydrate($originalValue)
    {
        if ($originalValue === null) {
            return null;
        }

        if (!$this->validate($originalValue)) {
            throw new \InvalidArgumentException();
        }

        return floatval($originalValue);
    }

    private function validate($originalValue)
    {
        return is_numeric($originalValue);
    }
}
