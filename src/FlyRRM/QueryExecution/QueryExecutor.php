<?php
namespace FlyRRM\QueryExecution;

interface QueryExecutor
{
    public function executeQuery($sql, array $params = array());
}
