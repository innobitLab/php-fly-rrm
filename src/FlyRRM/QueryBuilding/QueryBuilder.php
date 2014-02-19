<?php
namespace FlyRRM\QueryBuilding;

use FlyRRM\Mapping\Resource;

interface QueryBuilder
{
    public function buildQuery(Resource $resource);
    public function buildToManyQueries(Resource $resource);
}
