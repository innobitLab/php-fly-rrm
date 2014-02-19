<?php
namespace FlyRRM\Formatting\Field;

use FlyRRM\Mapping\Field;

interface FieldFormatterAbstractFactory
{
    public function buildFieldFormatterForField(Field $field);
}
