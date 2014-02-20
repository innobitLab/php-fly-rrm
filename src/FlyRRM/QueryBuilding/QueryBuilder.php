<?php
namespace FlyRRM\QueryBuilding;

use FlyRRM\Mapping\Resource;
use FlyRRM\Mapping\Relationship;

interface QueryBuilder
{
    public function buildQuery(Resource $resource);
    public function buildToManyQueries(Relationship $relationship);
}
