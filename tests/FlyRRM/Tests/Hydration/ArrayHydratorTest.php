<?php
namespace FlyRRM\Tests\Hydration;

use FlyRRM\Hydration\Field\FieldHydrationConcreteFactory;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;
use FlyRRM\Mapping\Field;
use FlyRRM\Hydration\ArrayHydrator;

class ArrayHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Hydration\ArrayHydrator $hydrator  */
    private $hydrator;

    protected function setUp()
    {
        $this->hydrator = new ArrayHydrator(new FieldHydrationConcreteFactory());
    }

    public function test_that_a_plain_data_array_is_hydrated_to_a_structured_array()
    {
        $plainArray = array(
            array('my__0_myCoolField' => 'my cool value!')
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array('myCoolField' => 'my cool value!')
            )
        );

        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $resource->addField($field);

        $resultArray = $this->hydrator->hydrate($plainArray, $resource);

        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_datetime_field_is_hydrated_to_a_datetime_instance()
    {
        $plainArray = array(
            array('my__0_myCoolDateTimeField' => '1987-04-07 06:30:12')
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array('myCoolDateTimeField' => new \DateTime('1987-04-07 06:30:12'))
            )
        );

        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolDateTimeField', 'my_cool_field', Field::TYPE_DATETIME);
        $resource->addField($field);

        $resultArray = $this->hydrator->hydrate($plainArray, $resource);

        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_date_field_is_hydrated_to_a_datetime_instance()
    {
        $plainArray = array(
            array('my__0_myCoolDateTimeField' => '1987-04-07')
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array('myCoolDateTimeField' => new \DateTime('1987-04-07 00:00:00'))
            )
        );

        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');

        $field = new Field($resource, 'myCoolDateTimeField', 'my_cool_field', Field::TYPE_DATE);
        $resource->addField($field);

        $resultArray = $this->hydrator->hydrate($plainArray, $resource);

        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_many_to_one_relationship_is_hydrated_to_a_structured_array()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'my_hot_id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'my_cool_table.hot_id = my_hot_table.id');
        $mainResource->addRelationship($relationship);

        $plainArray = array(
            array(
                'my__0_myCoolField' => 'my cool value!',
                'my__1_myHotField' => 'my hot value!'
            )
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        'myHotField' => 'my hot value!'
                    )
                )
            )
        );

        $resultArray = $this->hydrator->hydrate($plainArray, $mainResource);
        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_many_to_one_nested_relationship_is_hydrated_to_a_structured_array()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'my_hot_id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'my_cool_table.hot_id = my_hot_table.id');
        $mainResource->addRelationship($relationship);

        $thirdLevelResource = new Resource('my__2', 'myThirdResource', 'my_third_table', 'my_third_id');
        $thirdLevelResourceField = new Field($thirdLevelResource, 'myThirdField', 'my_third_field', Field::TYPE_STRING);
        $thirdLevelResource->addField($thirdLevelResourceField);

        $thirdLevelRelationship = new Relationship($referencedResource, $thirdLevelResource, 'many-to-one', 'my_hot_table.third_id = my_third_table.id');
        $referencedResource->addRelationship($thirdLevelRelationship);

        $plainArray = array(
            array(
                'my__0_myCoolField' => 'my cool value!',
                'my__1_myHotField' => 'my hot value!',
                'my__2_myThirdField' => 'my third value'
            )
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        'myHotField' => 'my hot value!',
                        'myThirdResource' => array(
                            'myThirdField' => 'my third value'
                        )
                    )
                )
            )
        );

        $resultArray = $this->hydrator->hydrate($plainArray, $mainResource);
        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_null_many_to_one_relationship_is_hydrated_to_a_structured_array()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'my_hot_id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);
        $referencedResourceField = new Field($referencedResource, 'myHotField2', 'my_hot_field_2', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'my_cool_table.hot_id = my_hot_table.id');
        $mainResource->addRelationship($relationship);

        $plainArray = array(
            array(
                'my__0_myCoolField' => 'my cool value!',
                'my__1_myHotField' => null,
                'my__1_myHotField2' => null
            )
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array (
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => null
                )
            )
        );

        $resultArray = $this->hydrator->hydrate($plainArray, $mainResource);
        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_one_to_many_relationship_is_hydrated_to_a_structured_array()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'one-to-many', 'cool_id');
        $mainResource->addRelationship($relationship);

        $plainArray = array(
            array(
                'my__0_myCoolField' => 'my cool value!',
                'my__1' => array(
                    array('my__1_myHotField' => 'clean'),
                    array('my__1_myHotField' => 'code')
                    ),
            ),
            array(
                'my__0_myCoolField' => 'my second cool value!',
                'my__1' => array(
                    array('my__1_myHotField' => 'tdd'),
                    array('my__1_myHotField' => 'rule'),
                    array('my__1_myHotField' => 'the world')
                ),
            )
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        array('myHotField' => 'clean'),
                        array('myHotField' => 'code'),
                    )
                ),
                array(
                    'myCoolField' => 'my second cool value!',
                    'myHotResource' => array(
                        array('myHotField' => 'tdd'),
                        array('myHotField' => 'rule'),
                        array('myHotField' => 'the world'),
                    )
                )
            )
        );

        $resultArray = $this->hydrator->hydrate($plainArray, $mainResource);
        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_nested_one_to_many_relationship_is_hydrated_to_a_structured_array()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'one-to-many', 'cool_id');
        $mainResource->addRelationship($relationship);

        $thirdLevelResource = new Resource('my__2', 'myThirdResource', 'my_third_table', 'id');
        $thirdLevelResourceField = new Field($thirdLevelResource, 'myThirdField', 'my_third_field', Field::TYPE_STRING);
        $thirdLevelResource->addField($thirdLevelResourceField);

        $thirdRelationship = new Relationship($referencedResource, $thirdLevelResource, 'one-to-many', 'hot_id');
        $referencedResource->addRelationship($thirdRelationship);

        $plainArray = array(
            array(
                'my__0_myCoolField' => 'my cool value!',
                'my__1' => array(
                    array(
                        'my__1_myHotField' => 'clean',
                        'my__2' => array(
                            array('my__2_myThirdField' => 'hello'),
                            array('my__2_myThirdField' => 'world')
                        ),
                    ),
                    array(
                        'my__1_myHotField' => 'code',
                        'my__2' => array(
                            array('my__2_myThirdField' => 'skills')
                        ),
                    ),

                ),
            ),
            array(
                'my__0_myCoolField' => 'my second cool value!',
                'my__1' => array(
                    array(
                        'my__1_myHotField' => 'tdd',
                        'my__2' => array(
                            array('my__2_myThirdField' => 'go fast')
                        ),
                    ),
                    array(
                        'my__1_myHotField' => 'rule',
                        'my__2' => array(
                            array('my__2_myThirdField' => 'go well')
                        ),
                    ),
                    array(
                        'my__1_myHotField' => 'the world',
                        'my__2' => array(
                            array('my__2_myThirdField' => 'test'),
                            array('my__2_myThirdField' => 'your'),
                            array('my__2_myThirdField' => 'code')
                        ),
                    ),
                ),
            )
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        array(
                            'myHotField' => 'clean',
                            'myThirdResource' => array(
                                array('myThirdField' => 'hello'),
                                array('myThirdField' => 'world')
                            )
                        ),
                        array(
                            'myHotField' => 'code',
                            'myThirdResource' => array(
                                array('myThirdField' => 'skills')
                            )
                        )
                    )
                ),
                array(
                    'myCoolField' => 'my second cool value!',
                    'myHotResource' => array(
                        array(
                            'myHotField' => 'tdd',
                            'myThirdResource' => array(
                                array('myThirdField' => 'go fast')
                            )
                        ),
                        array(
                            'myHotField' => 'rule',
                            'myThirdResource' => array(
                                array('myThirdField' => 'go well')
                            )
                        ),
                        array(
                            'myHotField' => 'the world',
                            'myThirdResource' => array(
                                array('myThirdField' => 'test'),
                                array('myThirdField' => 'your'),
                                array('myThirdField' => 'code')
                            )
                        )
                    )
                )
            )
        );

        $resultArray = $this->hydrator->hydrate($plainArray, $mainResource);
        $this->assertEquals($expectedStructuredArray, $resultArray);
    }

    public function test_that_a_null_one_to_many_relationship_is_hydrated_to_a_structured_array()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'my_cool_id');
        $mainResourceField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($mainResourceField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_table', 'my_hot_id');
        $referencedResourceField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);
        $referencedResourceField = new Field($referencedResource, 'myHotField2', 'my_hot_field_2', Field::TYPE_STRING);
        $referencedResource->addField($referencedResourceField);

        $relationship = new Relationship($mainResource, $referencedResource, 'one-to-many', 'cool_id');
        $mainResource->addRelationship($relationship);

        $plainArray = array(
            array(
                'my__0_myCoolField' => 'my cool value!',
                'my__1' => null,
            )
        );

        $expectedStructuredArray = array(
            'myCoolResource' => array(
                array (
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array()
                )
            )
        );

        $resultArray = $this->hydrator->hydrate($plainArray, $mainResource);
        $this->assertEquals($expectedStructuredArray, $resultArray);
    }
}
