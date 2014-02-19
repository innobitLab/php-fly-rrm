<?php
namespace FlyRRM\DataExtraction;

use FlyRRM\Mapping\Relationship;
use FlyRRM\Mapping\Resource;
use FlyRRM\QueryBuilding\QueryBuilder;
use FlyRRM\QueryExecution\QueryExecutor;

class DataExtractor
{
    private $queryBuilder;
    private $queryExecutor;

    public function __construct(QueryBuilder $queryBuilder, QueryExecutor $queryExecutor)
    {
        $this->queryBuilder = $queryBuilder;
        $this->queryExecutor = $queryExecutor;
    }

    public function extractData(Resource $resource)
    {
        $mainSql = $this->queryBuilder->buildQuery($resource);

        $mainData = $this->queryExecutor->executeQuery($mainSql);

        /** @var \FlyRRM\Mapping\Relationship $rel */
        foreach ($resource->getRelationships() as $rel) {
            if ($rel->getType() === Relationship::TYPE_ONE_TO_MANY) {
                $toManySqls = $this->queryBuilder->buildToManyQueries($resource);

                foreach ($toManySqls as $sql) {
                    foreach ($mainData as $rowIdx => $row) {
                        $mainResource = $rel->getMainResource();
                        $referencedResource = $rel->getReferencedResource();
                        $idKey = $mainResource->getResourceUniqueIdentifier() . '_' . $mainResource->getPrimaryKey();
                        $params = array(
                            ':' . $idKey => $row[$idKey]
                        );

                        $mainData[$rowIdx][$referencedResource->getResourceUniqueIdentifier()] =
                            $this->queryExecutor->executeQuery($sql, $params);
                    }
                }
            }
        }
        return $mainData;
    }
}
