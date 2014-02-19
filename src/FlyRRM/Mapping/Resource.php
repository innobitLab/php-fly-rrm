<?php
namespace FlyRRM\Mapping;

class Resource
{
    private $resourceUniqueIdentifier;
    private $alias;
    private $table;
    private $primaryKey;
    private $fields;
    private $relationships;

    public function getResourceUniqueIdentifier()
    {
        return $this->resourceUniqueIdentifier;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getRelationships()
    {
        return $this->relationships;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function __construct($resourceUniqueIdentifier, $alias, $table, $primaryKey)
    {
        $this->resourceUniqueIdentifier = $resourceUniqueIdentifier;
        $this->alias = $alias;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->fields = array();
        $this->relationships = array();
    }

    public function __toString()
    {
        return $this->alias;
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    public function addFieldsArray(array $fields)
    {
        foreach ($fields as $f) {
            $this->addField($f);
        }
    }

    public function countFields()
    {
        return sizeof($this->fields);
    }

    public function getFieldByIndex($index)
    {
        return $this->fields[$index];
    }

    public function addRelationship(Relationship $relationship)
    {
        $this->relationships[] = $relationship;
    }

    public function countRelationships()
    {
        return sizeof($this->relationships);
    }

    public function hasRelationships()
    {
        return $this->countRelationships() > 0;
    }

    public function getFieldByAlias($alias)
    {
        /** @var \FlyRRM\Mapping\Field $f */
        foreach ($this->fields as $f) {
            if ($f->getAlias() === $alias) {
                return $f;
            }
        }

        throw new \InvalidArgumentException(sprintf('cannot find field for alias \'%s\'', $alias));
    }

    public function getRelationshipByReferencedResourceAlias($referencedResourceAlias)
    {
        /** @var \FlyRRM\Mapping\Relationship $r */
        foreach ($this->relationships as $r) {
            if ($r->getReferencedResource()->getAlias() === $referencedResourceAlias) {
                return $r;
            }
        }

        throw new \InvalidArgumentException(sprintf('cannot find relationship for referenced resource alias \'%s\'', $referencedResourceAlias));
    }
}
