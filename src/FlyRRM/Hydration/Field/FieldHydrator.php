<?php
namespace FlyRRM\Hydration\Field;

interface FieldHydrator
{
    public function hydrate($originalValue);
}
