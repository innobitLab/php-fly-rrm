<?php
namespace FlyRRM\Hydration\Field;

class DateFieldHydrator extends DateTimeFieldHydrator implements FieldHydrator
{
    public function hydrate($originalValue)
    {
        if ($originalValue === null) {
            return null;
        }

        $originalValue .= ' 00:00:00';

        return parent::hydrate($originalValue);
    }
}
