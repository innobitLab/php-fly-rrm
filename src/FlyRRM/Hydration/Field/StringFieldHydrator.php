<?php
namespace FlyRRM\Hydration\Field;

class StringFieldHydrator implements FieldHydrator
{
    public function hydrate($originalValue)
    {
        if ($originalValue === null) {
            return null;
        }

        return (string)$originalValue;
    }
}
