<?php
namespace FlyRRM\Hydration\Field;

use FlyRRM\Mapping\Field;

class FieldHydrationConcreteFactory implements FieldHydrationAbstractFactory
{
    public function buildFieldHydratorForField(Field $field)
    {
        switch($field->getType()) {
            case Field::TYPE_STRING:
                return new StringFieldHydrator();
                break;

            case Field::TYPE_DATETIME:
                return new DateTimeFieldHydrator();
                break;

            case Field::TYPE_DATE:
                return new DateFieldHydrator();
                break;

            case Field::TYPE_NUMBER:
                return new NumberFieldHydrator();
                break;

            case Field::TYPE_BOOL:
                return new BoolFieldHydrator();
                break;

            default:
                throw new \InvalidArgumentException(sprintf('unknown field type[%s], cannot create related FieldHydrator', $field->getType()));
        }
    }
}
