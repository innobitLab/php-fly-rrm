<?php
namespace FlyRRM\Tests\QueryBuilding;

use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;
use FlyRRM\QueryBuilding\DBALQueryBuilder;
use FlyRRM\QueryBuilding\QueryBuilder;

class DBALQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var QueryBuilder */
    private $dbalQueryBuilder;

    protected function setUp()
    {
        $this->dbalQueryBuilder = new DBALQueryBuilder();
    }

    public function test_single_field_mapping_generated_query()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $resource->addField(new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING));

        $generatedSql = $this->dbalQueryBuilder->buildQuery($resource);
        $expectedSql = 'select my__0.my_cool_id as my__0_my_cool_id, my__0.my_cool_field as my__0_myCoolField from my_cool_table my__0';

        $this->assertEquals($expectedSql, $generatedSql);
    }

    public function test_multiple_fields_mapping_generated_query()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $fields = array(
            new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING),
            new Field($resource, 'myMoreCoolField', 'my_more_cool_field', Field::TYPE_STRING)
        );

        $resource->addFieldsArray($fields);

        $generatedSql = $this->dbalQueryBuilder->buildQuery($resource);
        $expectedSql = 'select my__0.my_cool_id as my__0_my_cool_id, my__0.my_cool_field as my__0_myCoolField, my__0.my_more_cool_field as my__0_myMoreCoolField from my_cool_table my__0';

        $this->assertEquals($expectedSql, $generatedSql);
    }

    public function test_resource_with_many_to_one_relationship_generated_query()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'hot_id');
        $mainResource->addRelationship($relationship);

        $generatedSql = $this->dbalQueryBuilder->buildQuery($mainResource);
        $expectedSql = 'select my__0.id as my__0_id, my__0.my_cool_field as my__0_myCoolField, my__1.id as my__1_id, my__1.my_hot_field as my__1_myHotField from my_cool_table my__0 left outer join my_hot_table my__1 on my__0.hot_id = my__1.id';

        $this->assertEquals($expectedSql, $generatedSql);
    }

    public function test_resource_with_many_to_one_nested_relationship_generated_query()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'hot_id');
        $mainResource->addRelationship($relationship);

        $thirdLevelResource = new Resource('my__2', 'myThirdResource', 'my_third_table', 'id');
        $thirdLevelResourceField = new Field($thirdLevelResource, 'myThirdField', 'my_third_field', Field::TYPE_STRING);
        $thirdLevelResource->addField($thirdLevelResourceField);

        $thirdLevelRelationship = new Relationship($referencedResource, $thirdLevelResource, 'many-to-one', 'third_id');
        $referencedResource->addRelationship($thirdLevelRelationship);

        $generatedSql = $this->dbalQueryBuilder->buildQuery($mainResource);
        $expectedSql = 'select my__0.id as my__0_id, my__0.my_cool_field as my__0_myCoolField, my__1.id as my__1_id, my__1.my_hot_field as my__1_myHotField, my__2.id as my__2_id, my__2.my_third_field as my__2_myThirdField from my_cool_table my__0 left outer join my_hot_table my__1 on my__0.hot_id = my__1.id left outer join my_third_table my__2 on my__1.third_id = my__2.id';

        $this->assertEquals($expectedSql, $generatedSql);
    }

    public function test_resource_with_one_to_many_relationship_generated_query()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'one-to-many', 'hot_id');
        $mainResource->addRelationship($relationship);

        $generatedToManySql = $this->dbalQueryBuilder->buildToManyQueries($relationship);
        $expectedToManySql = 'select my__1.id as my__1_id, my__1.my_hot_field as my__1_myHotField from my_hot_table my__1 where my__1.hot_id = :my__0_id';
        $this->assertEquals($expectedToManySql, $generatedToManySql);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage relationship must be of type one-to-many
     */
    public function test_that_to_many_queries_without_one_to_many_relationship_throws_exception()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, Relationship::TYPE_MANY_TO_ONE, 'hot_id');
        $mainResource->addRelationship($relationship);

        $generatedToManySql = $this->dbalQueryBuilder->buildToManyQueries($relationship);
    }
}
