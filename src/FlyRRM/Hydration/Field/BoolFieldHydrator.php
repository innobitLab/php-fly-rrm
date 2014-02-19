<?php
namespace FlyRRM\Hydration\Field;

class BoolFieldHydrator implements FieldHydrator
{
    public function hydrate($originalValue)
    {
        if ($originalValue === null) {
            return null;
        }

        if (!$this->validate($originalValue)) {
            throw new \InvalidArgumentException();
        }

        return (bool)$originalValue;
    }

    private function validate($originalValue)
    {
        if ($originalValue === 0 || $originalValue === 1) {
            return true;
        }

        $allowedValues = array('true', 'false', '0', '1');
        $originalValue = trim(strtolower($originalValue));

        return in_array($originalValue, $allowedValues, true);
    }
}
