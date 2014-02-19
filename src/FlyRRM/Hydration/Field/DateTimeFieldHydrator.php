<?php
namespace FlyRRM\Hydration\Field;

class DateTimeFieldHydrator implements FieldHydrator
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function hydrate($originalValue)
    {
        if ($originalValue === null) {
            return null;
        }

        if (!$this->validate($originalValue)) {
            throw new \InvalidArgumentException();
        }

        return \DateTime::createFromFormat(self::DATETIME_FORMAT, $originalValue);
    }

    private function validate($originalValue)
    {
        return \DateTime::createFromFormat(self::DATETIME_FORMAT, $originalValue);
    }
}
