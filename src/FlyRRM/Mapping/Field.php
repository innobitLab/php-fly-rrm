<?php
namespace FlyRRM\Mapping;

class Field
{
    const TYPE_STRING = 'string';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOL = 'bool';

    private $name;
    private $alias;
    private $type;
    private $resource;
    private $formatString;

    public function getName()
    {
        return $this->name;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getFormatString()
    {
        return $this->formatString;
    }

    public function __construct(Resource $resource, $alias, $name, $type, $formatString = null)
    {
        $this->resource = $resource;
        $this->alias = $alias;
        $this->name = $name;
        $this->type = $type;
        $this->formatString = $formatString;
    }

    public function __toString()
    {
        return $this->alias;
    }
}
