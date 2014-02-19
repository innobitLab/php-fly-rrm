<?php
namespace FlyRRM\Tests\DataExtraction;

use FlyRRM\DataExtraction\DataExtractor;
use FlyRRM\Mapping\Field;
use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;

class DataExtractionTest extends \PHPUnit_Framework_TestCase
{
    public function test_data_extractor_with_simple_resource()
    {
        $rootResource = new Resource('my__1', 'myCoolResource', 'my_cool_table', 'id');
        $rootField = new Field($rootResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $rootResource->addField($rootField);

        $sql = 'select my__1.id as my__1_id, my__1.my_cool_field as my__1_myCoolField from my_cool_table my__1';
        $data = array(
            array('my__1_id' => 1, 'my__1_myCoolField' => 'hello'),
            array('my__1_id' => 2, 'my__1_myCoolField' => 'world!'),
        );

        $queryBuilder = $this->getMock('\FlyRRM\QueryBuilding\QueryBuilder');
        $queryBuilder
            ->expects($this->once())
            ->method('buildQuery')
            ->with($this->equalTo($rootResource))
            ->will($this->returnValue($sql));

        $queryExecutor = $this->getMock('\FlyRRM\QueryExecution\QueryExecutor');
        $queryExecutor
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->equalTo($sql))
            ->will($this->returnValue($data));

        $dataExtractor = new DataExtractor($queryBuilder, $queryExecutor);
        $extractedData = $dataExtractor->extractData($rootResource);

        $this->assertEquals($data, $extractedData);
    }

    public function test_data_extractor_with_many_to_one_resource()
    {
        $rootResource = new Resource('my__1', 'myCoolResource', 'my_cool_table', 'id');
        $coolField = new Field($rootResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $rootResource->addField($coolField);

        $referencedResource = new Resource('my__2', 'myHotResource', 'my_hot_table', 'id');
        $hotField = new Field($referencedResource, 'myHotField', 'my_hot_field', Field::TYPE_STRING);
        $referencedResource->addField($hotField);

        $relationship = new Relationship($rootResource, $referencedResource, 'many-to-one', 'cool_id');
        $rootResource->addRelationship($relationship);

        $sql = 'select my__1.id as my__1_id, my__1.my_cool_field as my__1_myCoolField, my__2.id as my__2_id, my__2.my_hot_field as my__2_MyHotField from my_cool_table my__1 left outer join my_hot_table my__2 on my__2.cool_id = my__1.id';
        $data = array(
            array('my__1_id' => 1, 'my__1_myCoolField' => 'hello', 'my__2_id' => 1, 'my__2_myHotField' => 'clean'),
            array('my__1_id' => 2, 'my__1_myCoolField' => 'world!', 'my__2_id' => 3, 'my__2_myHotField' => 'code'),
        );

        $queryBuilder = $this->getMock('\FlyRRM\QueryBuilding\QueryBuilder');
        $queryBuilder
            ->expects($this->once())
            ->method('buildQuery')
            ->with($this->equalTo($rootResource))
            ->will($this->returnValue($sql));

        $queryExecutor = $this->getMock('\FlyRRM\QueryExecution\QueryExecutor');
        $queryExecutor
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->equalTo($sql))
            ->will($this->returnValue($data));

        $dataExtractor = new DataExtractor($queryBuilder, $queryExecutor);
        $extractedData = $dataExtractor->extractData($rootResource);

        $expectedData = array(
            array(
                'my__1_id' => 1,
                'my__1_myCoolField' => 'hello',
                'my__2' => array(
                    'my__2_id' => 1,
                    'my__2_myHotField' => 'clean'
                )
            ),
            array(
                'my__1_id' => 2,
                'my__1_myCoolField' => 'world!',
                'my__2' => array(
                    'my__2_id' => 3,
                    'my__2_myHotField' => 'code'
                )
            )
        );

        $this->assertEquals($data, $extractedData);
    }

    public function test_data_extractor_with_one_to_many_resource()
    {
        $rootResource = new Resource('my__1', 'myCoolResource', 'my_cool_table', 'id');
        $rootField = new Field($rootResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $rootResource->addField($rootField);

        $referencedResource = new Resource('my__2', 'myHotResource', 'my_hot_table', 'id');
        $coolField = new Field($referencedResource, 'myCoolField', 'my_cool_field', Field::TYPE_STRING);
        $referencedResource->addField($coolField);

        $relationship = new Relationship($rootResource, $referencedResource, 'one-to-many', 'cool_id');
        $rootResource->addRelationship($relationship);

        $mainSql = 'select my__1.id as my__1_id, my__1.my_cool_field as my__1_myCoolField from my_cool_table my__1';
        $mainData = array(
            array('my__1_id' => 1, 'my__1_myCoolField' => 'hello'),
            array('my__1_id' => 2, 'my__1_myCoolField' => 'world!'),
        );

        $toManyData1 = array(
            array('my__2_id' => 1, 'my__2_myHotField' => 'clean'),
            array('my__2_id' => 2, 'my__2_myHotField' => 'code'),
        );

        $toManyData2 = array(
            array('my__2_id' => 5, 'my__2_myHotField' => 'tdd'),
            array('my__2_id' => 6, 'my__2_myHotField' => 'rulez'),
        );

        $toManySqls = array('select my__2.id as my__2_id, my__2.my_hot_field as my__2_myHotField from my_hot_table my__2 where my__2.cool_id = :my__1_id');

        $queryBuilder = $this->getMock('\FlyRRM\QueryBuilding\QueryBuilder');
        $queryBuilder
            ->expects($this->at(0))
            ->method('buildQuery')
            ->with($this->equalTo($rootResource))
            ->will($this->returnValue($mainSql));
        $queryBuilder
            ->expects($this->at(1))
            ->method('buildToManyQueries')
            ->with($this->equalTo($rootResource))
            ->will($this->returnValue($toManySqls));

        $queryExecutor = $this->getMock('\FlyRRM\QueryExecution\QueryExecutor');
        $queryExecutor
            ->expects($this->at(0))
            ->method('executeQuery')
            ->with($this->equalTo($mainSql))
            ->will($this->returnValue($mainData));
        $queryExecutor
            ->expects($this->at(1))
            ->method('executeQuery')
            ->with($this->equalTo($toManySqls[0]), $this->equalTo(array(':my__1_id' => 1)))
            ->will($this->returnValue($toManyData1));
        $queryExecutor
            ->expects($this->at(2))
            ->method('executeQuery')
            ->with($this->equalTo($toManySqls[0]), $this->equalTo(array(':my__1_id' => 2)))
            ->will($this->returnValue($toManyData2));

        $dataExtractor = new DataExtractor($queryBuilder, $queryExecutor);
        $extractedData = $dataExtractor->extractData($rootResource);

        $expectedData = $mainData;
        $expectedData[0]['my__2'] = $toManyData1;
        $expectedData[1]['my__2'] = $toManyData2;

        $this->assertEquals($expectedData, $extractedData);
    }
}
