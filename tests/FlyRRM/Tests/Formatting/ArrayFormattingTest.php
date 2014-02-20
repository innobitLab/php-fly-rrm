<?php
namespace FlyRRM\Tests\Formatting;

use FlyRRM\Formatting\ArrayFormatter;
use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;
use FlyRRM\Formatting\Field\FieldFormatterConcreteFactory;

class ArrayFormattingTest extends \PHPUnit_Framework_TestCase
{
    /** @var \FlyRRM\Formatting\ArrayFormatter */
    private $arrayFormatter;

    protected function setUp()
    {
        $factory = new FieldFormatterConcreteFactory();
        $this->arrayFormatter = new ArrayFormatter($factory);
    }

    public function test_that_a_structured_data_is_formatted()
    {
        $resource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');

        $stringField = new Field($resource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $resource->addField($stringField);

        $dateField = new Field($resource, 'myDateField', 'my_date_field', Field::TYPE_DATE, 'd/m/Y');
        $resource->addField($dateField);

        $datetimeField = new Field($resource, 'myDateTimeField', 'my_datetime_field', Field::TYPE_DATETIME, 'd/m/Y H:i:s');
        $resource->addField($datetimeField);

        $numberField = new Field($resource, 'myNumberField', 'my_number_field', Field::TYPE_NUMBER);
        $resource->addField($numberField);

        $structuredData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myDateField' => new \DateTime('2014-02-18'),
                    'myDateTimeField' => new \DateTime('2014-02-18 13:57:12'),
                    'myNumberField' => 1250.23
                )
            )
        );

        $expectedData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myDateField' => '18/02/2014',
                    'myDateTimeField' => '18/02/2014 13:57:12',
                    'myNumberField' => 1250.23
                )
            )
        );

        $formattedData = $this->arrayFormatter->format($structuredData, $resource);

        $this->assertEquals($expectedData, $formattedData);
    }

    public function test_that_a_structured_data_with_many_to_one_relationship_is_formatted()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $stringField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($stringField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_resource', 'id');
        $dateField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_DATE, 'd/m/Y');
        $referencedResource->addField($dateField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'cool_id');
        $mainResource->addRelationship($relationship);

        $structuredData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        'myHotField' => new \DateTime('2014-02-18')
                    )
                )
            )
        );

        $expectedData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        'myHotField' => '18/02/2014'
                    )
                )
            )
        );

        $outputData = $this->arrayFormatter->format($structuredData, $mainResource);
        $this->assertEquals($expectedData, $outputData);
    }

    public function test_that_a_structured_data_with_null_many_to_one_relationship_is_formatted()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $stringField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($stringField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_resource', 'id');
        $dateField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_DATE, 'd/m/Y');
        $referencedResource->addField($dateField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'cool_id');
        $mainResource->addRelationship($relationship);

        $structuredData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => null
                )
            )
        );

        $expectedData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => null
                )
            )
        );

        $outputData = $this->arrayFormatter->format($structuredData, $mainResource);
        $this->assertEquals($expectedData, $outputData);
    }

    public function test_that_a_structured_data_with_one_to_many_relationship_is_formatted()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $stringField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($stringField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_resource', 'id');
        $dateField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_DATE, 'd/m/Y');
        $referencedResource->addField($dateField);

        $relationship = new Relationship($mainResource, $referencedResource, 'one-to-many', 'cool_id');
        $mainResource->addRelationship($relationship);

        $structuredData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        array('myHotField' => new \DateTime('2014-02-18')),
                        array('myHotField' => new \DateTime('2014-02-19')),
                        array('myHotField' => new \DateTime('2014-02-20'))
                    )
                )
            )
        );

        $expectedData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        array('myHotField' => '18/02/2014'),
                        array('myHotField' => '19/02/2014'),
                        array('myHotField' => '20/02/2014')
                    )
                )
            )
        );

        $outputtedData = $this->arrayFormatter->format($structuredData, $mainResource);

        $this->assertEquals($expectedData, $outputtedData);
    }

    public function test_that_a_structured_data_with_nested_many_to_one_relationship_is_formatted()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $stringField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($stringField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_resource', 'id');
        $dateField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_DATE, 'd/m/Y');
        $referencedResource->addField($dateField);

        $relationship = new Relationship($mainResource, $referencedResource, 'many-to-one', 'cool_id');
        $mainResource->addRelationship($relationship);

        $thirdResource = new Resource('my__2', 'myThirdResource', 'my_third_resource', 'id');
        $thirdField = new Field($thirdResource, 'myThirdField', 'my_third_field', Field::TYPE_NUMBER);
        $thirdResource->addField($thirdField);

        $thirdRelationship = new Relationship($referencedResource, $thirdResource, 'many-to-one', 'hot_id');
        $referencedResource->addRelationship($thirdRelationship);

        $structuredData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        'myHotField' => new \DateTime('2014-02-18'),
                        'myThirdResource' => array(
                            'myThirdField' => 1250.23
                        )
                    )
                )
            )
        );

        $expectedData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        'myHotField' =>'18/02/2014',
                        'myThirdResource' => array(
                            'myThirdField' => 1250.23
                        )
                    )
                )
            )
        );

        $outputtedData = $this->arrayFormatter->format($structuredData, $mainResource);
        $this->assertEquals($expectedData, $outputtedData);
    }

    public function test_that_a_structured_data_with_nested_one_to_many_relationship_is_formatted()
    {
        $mainResource = new Resource('my__0', 'myCoolResource', 'my_cool_table', 'id');
        $stringField = new Field($mainResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $mainResource->addField($stringField);

        $referencedResource = new Resource('my__1', 'myHotResource', 'my_hot_resource', 'id');
        $dateField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_DATE, 'd/m/Y');
        $referencedResource->addField($dateField);

        $relationship = new Relationship($mainResource, $referencedResource, 'one-to-many', 'cool_id');
        $mainResource->addRelationship($relationship);

        $thirdResource = new Resource('my__2', 'myThirdResource', 'my_third_resource', 'id');
        $thirdField = new Field($thirdResource, 'myThirdField', 'my_third_field', Field::TYPE_NUMBER);
        $thirdResource->addField($thirdField);

        $thirdRelationship = new Relationship($referencedResource, $thirdResource, 'one-to-many', 'hot_id');
        $referencedResource->addRelationship($thirdRelationship);

        $structuredData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        array(
                            'myHotField' => new \DateTime('2014-02-18'),
                            'myThirdResource' => array(
                                array('myThirdField' => 10),
                                array('myThirdField' => 20),
                                array('myThirdField' => 30),
                            )
                        ),
                        array(
                            'myHotField' => new \DateTime('2014-02-19'),
                            'myThirdResource' => array(
                                array('myThirdField' => 10.10)
                            )
                        ),
                        array(
                            'myHotField' => new \DateTime('2014-02-20'),
                            'myThirdResource' => array()
                        )
                    )
                )
            )
        );

        $expectedData = array(
            'myCoolResource' => array(
                array(
                    'myCoolField' => 'my cool value!',
                    'myHotResource' => array(
                        array(
                            'myHotField' => '18/02/2014',
                            'myThirdResource' => array(
                                array('myThirdField' => 10),
                                array('myThirdField' => 20),
                                array('myThirdField' => 30),
                            )
                        ),
                        array(
                            'myHotField' => '19/02/2014',
                            'myThirdResource' => array(
                                array('myThirdField' => 10.10)
                            )
                        ),
                        array(
                            'myHotField' => '20/02/2014',
                            'myThirdResource' => array()
                        )
                    )
                )
            )
        );

        $outputtedData = $this->arrayFormatter->format($structuredData, $mainResource);
        $this->assertEquals($expectedData, $outputtedData);
    }
}
