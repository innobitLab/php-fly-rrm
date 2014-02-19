<?php
namespace FlyRRM\Tests\Mapping;

use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class RelationshipTest extends \PHPUnit_Framework_TestCase
{
    public function test_relationship_construction()
    {
        $rootResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $referencedResource = new Resource('my__1', 'myRelatedResource', 'my_related_table', 'my_related_id');

        $relationship = new Relationship($rootResource, $referencedResource, 'many-to-one', 'my_cool_id');

        $this->assertEquals($rootResource, $relationship->getMainResource());
        $this->assertEquals($referencedResource, $relationship->getReferencedResource());
        $this->assertEquals(Relationship::TYPE_MANY_TO_ONE, $relationship->getType());
        $this->assertEquals('my_cool_id', $relationship->getJoinColumn());
    }
}
