<?php
namespace FlyRRM\Tests\Mapping;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function test_resource_construction()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $this->assertEquals('my__0', $resource->getResourceUniqueIdentifier());
        $this->assertEquals('myCoolResource', $resource->getAlias());
        $this->assertEquals('my_cool_table', $resource->getTable());
        $this->assertEquals('my_cool_id', $resource->getPrimaryKey());
    }

    public function test_that_a_resource_to_string_returns_alias()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $this->assertEquals('myCoolResource', $resource->__toString());
    }

    public function test_adding_a_field_to_a_resource()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $resource->addField($field);

        $this->assertEquals(1, $resource->countFields());
    }

    public function test_adding_multiple_fields_to_a_resource()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $otherField = new Field($resource, 'myOtherCoolField', 'my_other_cool_field', Field::TYPE_STRING);

        $resource->addFieldsArray(array($field, $otherField));

        $this->assertEquals(2, $resource->countFields());
    }

    public function test_getting_a_field_by_index()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $resource->addField($field);

        $this->assertEquals($field, $resource->getFieldByIndex(0));
    }

    public function test_getting_a_field_by_alias()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $resource->addField($field);

        $this->assertEquals($field, $resource->getFieldByAlias('myCoolField'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage cannot find field for alias 'myUnknownField'
     */
    public function test_that_getting_a_field_by_unknown_alias_throws_exception()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $resource->addField($field);

        $this->assertEquals($field, $resource->getFieldByAlias('myUnknownField'));
    }

    public function test_adding_a_relationship_to_resource()
    {
        $rootResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $referencedResource = new Resource('my__1', 'myOtherResource', 'my_other_table', 'my_cool_id');

        $relationship = new Relationship($rootResource, $referencedResource, 'many-to-one', 'my_other_table.cool_id = my_cool_table.id');
        $rootResource->addRelationship($relationship);

        $this->assertTrue($rootResource->hasRelationships());
        $this->assertEquals(1, $rootResource->countRelationships());
    }

    public function test_getting_a_relationship_by_referenced_resource_alias()
    {
        $rootResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $referencedResource = new Resource('my__1', 'myOtherResource', 'my_other_table', 'my_cool_id');

        $relationship = new Relationship($rootResource, $referencedResource, 'many-to-one', 'my_other_table.cool_id = my_cool_table.id');
        $rootResource->addRelationship($relationship);

        $this->assertEquals($relationship, $rootResource->getRelationshipByReferencedResourceAlias('myOtherResource'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage cannot find relationship for referenced resource alias 'myUnknownResource'
     */
    public function test_that_getting_a_relationship_by_unknown_referenced_resource_alias_throws_exception()
    {
        $rootResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $referencedResource = new Resource('my__1', 'myOtherResource', 'my_other_table', 'my_cool_id');

        $relationship = new Relationship($rootResource, $referencedResource, 'many-to-one', 'my_other_table.cool_id = my_cool_table.id');
        $rootResource->addRelationship($relationship);

        $this->assertEquals($relationship, $rootResource->getRelationshipByReferencedResourceAlias('myUnknownResource'));
    }
}
