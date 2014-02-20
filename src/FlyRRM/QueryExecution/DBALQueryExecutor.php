<?php
namespace FlyRRM\QueryExecution;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class DBALQueryExecutor implements QueryExecutor
{
    private $databaseConfiguration;

    public function __construct(DatabaseConfiguration $config)
    {
        $this->databaseConfiguration = $config;
    }

    public function executeQuery($sql, array $params = array())
    {
        $connection = $this->createDBConnection();
        $stmt = $connection->prepare($sql);

        foreach ($params as $pKey => $pValue) {
            $stmt->bindValue($pKey, $pValue);
        }

        $stmt->execute();
        $connection->close();
        return $stmt->fetchAll();
    }

    private function createDBConnection()
    {
        $dbalConf = new Configuration();

        $connectionParams = array(
            'driver' => $this->databaseConfiguration->getDriver(),
            'host' => $this->databaseConfiguration->getHost(),
            'port' => $this->databaseConfiguration->getPort(),
            'user' => $this->databaseConfiguration->getUsername(),
            'password' => $this->databaseConfiguration->getPassword(),
            'dbname' => $this->databaseConfiguration->getDatabaseName()
        );

        return DriverManager::getConnection($connectionParams, $dbalConf);
    }
}
