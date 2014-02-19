<?php
namespace FlyRRM\Formatting\Field;

class DateTimeFieldFormatter
{
    const DEFAULT_DATETIME_FORMAT_STRING = 'Y-m-d H:i:s';
    const DEFAULT_DATE_FORMAT_STRING = 'Y-m-d';

    private $formatString;

    public function getFormatString()
    {
        return $this->formatString;
    }

    public function __construct($formatString = null)
    {
        $this->formatString = $formatString !== null ? $formatString : self::DEFAULT_DATETIME_FORMAT_STRING;
    }

    public function format($rawValue)
    {
        if ($rawValue === null) {
            return null;
        }

        if (!($rawValue instanceof \DateTime)) {
            throw new \InvalidArgumentException();
        }

        return $rawValue->format($this->formatString);
    }
}
