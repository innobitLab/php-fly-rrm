<?php
namespace FlyRRM\Tests\Integration;

use FlyRRM\DataExtraction\DataExtractor;
use FlyRRM\Hydration\ArrayHydrator;
use FlyRRM\Hydration\Field\FieldHydrationConcreteFactory;
use FlyRRM\Mapping\Parsing\Yaml\YamlMappingParser;
use FlyRRM\QueryBuilding\DBALQueryBuilder;
use FlyRRM\QueryExecution\DatabaseConfiguration;
use FlyRRM\QueryExecution\DBALQueryExecutor;
use FlyRRM\Tests\DbUnit_ArrayDataSet;

require_once 'DbUnit_ArrayDataSet.php';

class IntegrationTest extends \PHPUnit_Extensions_Database_TestCase
{
    static private $pdo = null;
    private $conn = null;

    public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                $dsn = 'mysql:dbname=' . $GLOBALS['DB_DBNAME'] .';host=' . $GLOBALS['DB_HOST'];
                self::$pdo = new \PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    protected function getDataSet()
    {
        return new DbUnit_ArrayDataSet(array(
            'users' => array(
                array(
                    'id' => 1,
                    'username' => 'admin',
                    'hash_password' => '1459dd5a89d5874a673c109d4627291b854c7917',
                    'firstname' => 'Mark',
                    'lastname' => 'Green'),

                array(
                    'id' => 2,
                    'username' => 'g.white',
                    'hash_password' => '01426d69f3d0b179566635b2a69561a486c3660a',
                    'firstname' => 'Gabriel',
                    'lastname' => 'White'),
            ),

            'contracts' => array(
                array(
                    'id' => 1,
                    'name' => 'intern',
                    'id_user_creator' => 1,
                    'id_user_last_edit' => null,
                    'created' => '2014-02-01 10:29:12',
                    'edited' => null),

                array(
                    'id' => 2,
                    'name' => 'full-time',
                    'id_user_creator' => 1,
                    'id_user_last_edit' => 2,
                    'created' => '2014-02-01 10:29:50',
                    'edited' => '2014-02-01 10:29:59')
            ),

            'banks' => array(
                array(
                    'id' => 1,
                    'name' => 'global bank',
                ),
                array(
                    'id' => 2,
                    'name' => 'money back bank',
                ),
            ),

            'employees' => array(
                array(
                    'id' => 1,
                    'name' => 'Mario',
                    'surname' => 'Rossi',
                    'code' => 'ABC002',
                    'birthday' => '1987-04-07',
                    'id_contract' => 2,
                    'id_user_creator' => 2,
                    'id_user_last_edit' => 1,
                    'created' => '2014-02-10 14:41:23',
                    'edited' => '2014-02-10 14:44:59'),

                array(
                    'id' => 2,
                    'name' => 'Giovanni',
                    'surname' => 'Verdi',
                    'code' => null,
                    'birthday' => null,
                    'id_contract' => 1,
                    'id_user_creator' => 1,
                    'id_user_last_edit' => null,
                    'created' => '2014-02-10 14:50:23',
                    'edited' => null),
            ),

            'payments' => array(
                array(
                    'id' => 1,
                    'value' => 1000,
                    'id_employee' => 1,
                    'id_bank' => 1),
                array(
                    'id' => 2,
                    'value' => 2000,
                    'id_employee' => 1,
                    'id_bank' => 1),
                array(
                    'id' => 3,
                    'value' => 3000,
                    'id_employee' => 1,
                    'id_bank' => 1),
                array(
                    'id' => 4,
                    'value' => 1500,
                    'id_employee' => 2,
                    'id_bank' => 2),
            )
        ));
    }

    public function test_end_to_end_components_integration()
    {
        $yamlMapping = <<<EOT
resource:
    alias: 'impiegati'
    table: 'employees'
    primary-key: 'id'

    fields:
        -
            name: 'name'
            alias: 'nome'

        -
            name: 'surname'
            alias: 'cognome'

        -
            name: 'code'
            alias: 'codice'

        -
            name: 'birthday'
            alias: 'dataNascita'
            type: 'date'

        -
            name: 'created'
            alias: 'creatoIl'
            type: 'datetime'

        -
            name: 'edited'
            alias: 'modificatoIl'
            type: 'datetime'

    relationships:
        -
            type: 'many-to-one'
            join-column: 'id_user_creator'

            resource:
                alias: 'utenteCreazione'
                table: 'users'
                primary-key: 'id'

                fields:
                    -
                        name: 'username'
                        alias: 'Username'

                    -
                        name: 'firstname'
                        alias: 'Nome'

                    -
                        name: 'lastname'
                        alias: 'Cognome'


        -
            type: 'many-to-one'
            join-column: 'id_user_last_edit'

            resource:
                alias: 'utenteUltimaModifica'
                table: 'users'
                primary-key: 'id'

                fields:
                    -
                        name: 'username'
                        alias: 'Username'

                    -
                        name: 'firstname'
                        alias: 'Nome'

                    -
                        name: 'lastname'
                        alias: 'Cognome'

        -
            type: 'many-to-one'
            join-column: 'id_contract'

            resource:
                alias: 'contratto'
                table: 'contracts'
                primary-key: 'id'

                fields:
                    -
                        name: 'name'
                        alias: 'nome'

                    -
                        name: 'created'
                        alias: 'creatoIl'
                        type: 'datetime'

                    -
                        name: 'edited'
                        alias: 'modificatoIl'
                        type: 'datetime'

                relationships:
                    -
                        type: 'many-to-one'
                        join-column: 'id_user_creator'

                        resource:
                            alias: 'utenteCreazione'
                            table: 'users'
                            primary-key: 'id'

                            fields:
                                -
                                    name: 'username'
                                    alias: 'Username'

                                -
                                    name: 'firstname'
                                    alias: 'Nome'

                                -
                                    name: 'lastname'
                                    alias: 'Cognome'


                    -
                        type: 'many-to-one'
                        join-column: 'id_user_last_edit'

                        resource:
                            alias: 'utenteUltimaModifica'
                            table: 'users'
                            primary-key: 'id'

                            fields:
                                -
                                    name: 'username'
                                    alias: 'Username'

                                -
                                    name: 'firstname'
                                    alias: 'Nome'

                                -
                                    name: 'lastname'
                                    alias: 'Cognome'

        -
            type: 'one-to-many'
            join-column: 'id_employee'

            resource:
                alias: 'pagamenti'
                table: 'payments'
                primary-key: 'id'

                fields:
                    -
                        name: 'value'
                        alias: 'Valore'
                        type: 'number'

                relationships:

                    -
                        type: 'many-to-one'
                        join-column: 'id_bank'

                        resource:
                            alias: 'banca'
                            table: 'banks'
                            primary-key: 'id'

                            fields:
                                -
                                    name: 'name'
                                    alias: 'Nome'

EOT;

        $parser = new YamlMappingParser();
        $resource = $parser->parse($yamlMapping);

        $dbalQueryBuilder = new DBALQueryBuilder();

        $databaseConf = new DatabaseConfiguration();
        $databaseConf->setDatabaseName($GLOBALS['DB_DBNAME']);
        $databaseConf->setDriver('pdo_mysql');
        $databaseConf->setHost($GLOBALS['DB_HOST']);
        $databaseConf->setPassword($GLOBALS['DB_PASSWD']);
        $databaseConf->setPort($GLOBALS['DB_PORT']);
        $databaseConf->setUsername($GLOBALS['DB_USER']);
        $queryExecutor = new DBALQueryExecutor($databaseConf);

        $dataExtractor = new DataExtractor($dbalQueryBuilder, $queryExecutor);

        $plainData = $dataExtractor->extractData($resource);

        $dataHydrator = new ArrayHydrator(new FieldHydrationConcreteFactory());
        $hydratedData = $dataHydrator->hydrate($plainData, $resource);

        $expectedData = array(
            'impiegati' => array(
                0 => array(
                        'nome' => 'Mario',
                        'cognome' => 'Rossi',
                        'codice' => 'ABC002',
                        'dataNascita' => new \DateTime('1987-04-07'),
                        'creatoIl' => new \DateTime('2014-02-10 14:41:23'),
                        'modificatoIl' => new \DateTime('2014-02-10 14:44:59'),
                        'utenteCreazione' => array(
                            'Username' => 'g.white',
                            'Nome' => 'Gabriel',
                            'Cognome' => 'White'
                        ),
                        'utenteUltimaModifica' => array(
                            'Username' => 'admin',
                            'Nome' => 'Mark',
                            'Cognome' => 'Green'
                        ),
                        'contratto' => array(
                            'nome' => 'full-time',
                            'creatoIl' => new \DateTime('2014-02-01 10:29:50'),
                            'modificatoIl' => new \DateTime('2014-02-01 10:29:59'),
                            'utenteCreazione' => array(
                                'Username' => 'admin',
                                'Nome' => 'Mark',
                                'Cognome' => 'Green'
                            ),
                            'utenteUltimaModifica' => array(
                                'Username' => 'g.white',
                                'Nome' => 'Gabriel',
                                'Cognome' => 'White'
                            ),
                        ),
                        'pagamenti' => array(
                            array(
                                'Valore' => 1000.00,
                                'banca' => array(
                                    'Nome' => 'global bank'
                                )
                            ),
                            array(
                                'Valore' => 2000.00,
                                'banca' => array(
                                    'Nome' => 'global bank'
                                )
                            ),
                            array(
                                'Valore' => 3000.00,
                                'banca' => array(
                                    'Nome' => 'global bank'
                                )
                            ),
                        )
                ),

                1 => array(
                        'nome' => 'Giovanni',
                        'cognome' => 'Verdi',
                        'codice' => null,
                        'dataNascita' => null,
                        'creatoIl' => new \DateTime('2014-02-10 14:50:23'),
                        'modificatoIl' => null,
                        'utenteCreazione' => array(
                            'Username' => 'admin',
                            'Nome' => 'Mark',
                            'Cognome' => 'Green'
                        ),
                        'utenteUltimaModifica' => null,
                        'contratto' => array(
                            'nome' => 'intern',
                            'creatoIl' => new \DateTime('2014-02-01 10:29:12'),
                            'modificatoIl' => null,
                            'utenteCreazione' => array(
                                'Username' => 'admin',
                                'Nome' => 'Mark',
                                'Cognome' => 'Green'
                            ),
                            'utenteUltimaModifica' => null
                        ),
                        'pagamenti' => array(
                            array(
                                'Valore' => 1500,
                                'banca' => array(
                                    'Nome' => 'money back bank'
                                )
                            )
                        )
                )
            )
        );

        $this->assertEquals($expectedData, $hydratedData);
    }

    public function test_end_to_end_with_simple_where()
    {

        $yamlMapping = <<<EOT
resource:
    alias: 'impiegati'
    table: 'employees'
    primary-key: 'id'

    fields:
        -
            name: 'name'
            alias: 'nome'

        -
            name: 'surname'
            alias: 'cognome'

        -
            name: 'code'
            alias: 'codice'

        -
            name: 'birthday'
            alias: 'dataNascita'
            type: 'date'

        -
            name: 'created'
            alias: 'creatoIl'
            type: 'datetime'

        -
            name: 'edited'
            alias: 'modificatoIl'
            type: 'datetime'
    where: 'id=2'

EOT;

        $parser = new YamlMappingParser();
        $resource = $parser->parse($yamlMapping);

        $dbalQueryBuilder = new DBALQueryBuilder();

        $databaseConf = new DatabaseConfiguration();
        $databaseConf->setDatabaseName($GLOBALS['DB_DBNAME']);
        $databaseConf->setDriver('pdo_mysql');
        $databaseConf->setHost($GLOBALS['DB_HOST']);
        $databaseConf->setPassword($GLOBALS['DB_PASSWD']);
        $databaseConf->setPort($GLOBALS['DB_PORT']);
        $databaseConf->setUsername($GLOBALS['DB_USER']);
        $queryExecutor = new DBALQueryExecutor($databaseConf);

        $dataExtractor = new DataExtractor($dbalQueryBuilder, $queryExecutor);

        $plainData = $dataExtractor->extractData($resource);

        $this->assertEquals(1, sizeof($plainData));

    }

}
