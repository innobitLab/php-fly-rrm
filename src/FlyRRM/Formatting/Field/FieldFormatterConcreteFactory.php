<?php
namespace FlyRRM\Formatting\Field;

use FlyRRM\Mapping\Field;

class FieldFormatterConcreteFactory implements FieldFormatterAbstractFactory
{
    public function buildFieldFormatterForField(Field $field)
    {
        switch ($field->getType()) {
            case Field::TYPE_DATE:
            case Field::TYPE_DATETIME:
                $formatString = $field->getFormatString();

                if ($formatString === null) {
                    $formatString = $field->getType() === Field::TYPE_DATE ?
                        DateTimeFieldFormatter::DEFAULT_DATE_FORMAT_STRING :
                        DateTimeFieldFormatter::DEFAULT_DATETIME_FORMAT_STRING;
                }

                return new DateTimeFieldFormatter($formatString);
                break;

            case Field::TYPE_STRING:
                return new StringFieldFormatter();

            case Field::TYPE_NUMBER:
                return new NumberFieldFormatter();

            default:
                throw new \InvalidArgumentException(sprintf('unknown field type[%s], cannot create related formatter', $field->getType()));
        }
    }
}
