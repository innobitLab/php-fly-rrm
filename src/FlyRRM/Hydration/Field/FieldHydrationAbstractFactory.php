<?php
namespace FlyRRM\Hydration\Field;

use FlyRRM\Mapping\Field;

interface FieldHydrationAbstractFactory
{
    public function buildFieldHydratorForField(Field $field);
}
