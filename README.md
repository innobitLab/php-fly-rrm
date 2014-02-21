# php-fly-rrm

[![Build Status](https://travis-ci.org/innobitLab/php-fly-rrm.png)](https://travis-ci.org/innobitLab/php-fly-rrm)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d4bded3a-1dd5-4c14-9d54-0bb35fc62d5d/mini.png)](https://insight.sensiolabs.com/projects/d4bded3a-1dd5-4c14-9d54-0bb35fc62d5d)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/innobitLab/php-fly-rrm/badges/quality-score.png?s=bfaec297688a7291fed99fcc2643f2bcd45fd31c)](https://scrutinizer-ci.com/g/innobitLab/php-fly-rrm/)
[![Code Coverage](https://scrutinizer-ci.com/g/innobitLab/php-fly-rrm/badges/coverage.png?s=809705bc11aa61d9bd2a729acda50fe1f78df964)](https://scrutinizer-ci.com/g/innobitLab/php-fly-rrm/)

An entity free "RRM" (Resource Relational Mapping) to extract structured data from relational DBMS

## Why a Resource Relational Mapping

Because sometimes you need to extract data from a relational DBMS in a **structured** way, and maybe you don't want to add an "heavy" ORM with all his dependencies... or you can't beacuse of a legacy context.

## Give me an example

Ready, set, go!

Suppose we have this type of database:

**users**

id | username | password
--- | --- | --- 
1 | admin | mysecret 
2 | james.white | whitesnow 

**contracts**

id | name
--- | --- 
1 | full-time 
2 | half-time 

**banks**

id | name
--- | --- 
1 | the money free bank
2 | super bank 
2 | momeny first bank 

**employees**

id | name | surname | id_contract | id_user_creator | id_user_last_edit 
--- | --- | --- | --- | --- | ---
1 | Mario | Rossi | 2 | 1 | null 
2 | Giuseppe | Verdi | 1 | 1 | 2

**payments**

id | value | id_employee | id_bank
--- | --- | --- | --- 
1 | 1200.23 | 1 | 2 
1 | 1540 | 2 | 3 
1 | 1240.23 | 1 | 2 

and you have been asked to export this data in a similar structured json format:

```javascript
{
   "employees":[
      {
         "name":"Mario",
         "surname":"Rossi",
         "contract":{
            "name":"half-time"
         },
         "creator":{
            "username":"admin"
         },
         "last-edit":null,
         "payments":[
            {
               "value":1200.23,
               "bank":{
                  "name":"super bank"
               }
            },
            {
               "value":1240.23,
               "bank":{
                  "name":"super bank"
               }
            }
         ]
      },
      {
         "name":"Giuseppe",
         "surname":"Verdi",
         "contract":{
            "name":"full-time"
         },
         "creator":{
            "username":"admin"
         },
         "last-edit":{
            "username":"james.white"
         },
         "payments":[
            {
               "value":1540,
               "bank":{
                  "name":"money first bank"
               }
            }
         ]
      }
   ]
}
```

and you have been asked to make this export configurable via a similar YAML file:

``` yaml
resource:
	alias: 'employees'
	table: 'employees'
	primary-key: 'id'

	fields:
		-
			name: 'name'
		-
			name: 'surname'

	relationships:
		-
			type: 'many-to-one'
			join-column: 'id_contract'

			resource:
				alias: 'contract'
				table: 'contracts'
				primary-key: 'id'

				fields:
					-
						name: 'name'

		-
			type: 'many-to-one'
			join-column: 'id_user_creation'

			resource:
				alias: 'creator'
				table: 'users'
				primary-key: 'id'

				fields:
					-
						name: 'username'

		-
			type: 'many-to-one'
			join-column: 'id_user_last_edit'

			resource:
				alias: 'last-edit'
				table: 'users'
				primary-key: 'id'

				fields:
					-
						name: 'username'

		-
			type: 'one-to-many'
			join-column: 'id_employee'

			resource:
				alias: 'payments'
				table: 'payments'
				primary-key: 'id'

				fields:
					-	
						name: 'value'
						type: 'number'

				relationships:
					-
						type: 'many-to-one'
						join-column: 'id_bank'
						primary-key: 'id'

						fields:
							-
								name: 'name'
```


## Installation ##

### Composer ###

You can install `innobitlab/php-fly-rrm` using [composer](http://getcomposer.org/) Dependency Manager.

If you need information about installing composer: [http://getcomposer.org/doc/00-intro.md#installation-nix](http://getcomposer.org/doc/00-intro.md#installation-nix)

Add this to your composer.json file:

	{
    	"require": {
        	"innobitlab/php-fly-rrm": "dev-master"
    	}
	}

## Usage ##

### Without facade (more flexibility)

``` php
<?php

use FlyRRM\DataExtraction\DataExtractor;
use FlyRRM\Formatting\ArrayFormatter;
use FlyRRM\Formatting\Field\FieldFormatterConcreteFactory;
use FlyRRM\Hydration\ArrayHydrator;
use FlyRRM\Hydration\Field\FieldHydrationConcreteFactory;
use FlyRRM\Mapping\Parsing\Yaml\YamlMappingParser;
use FlyRRM\QueryBuilding\DBALQueryBuilder;
use FlyRRM\QueryExecution\DatabaseConfiguration;
use FlyRRM\QueryExecution\DBALQueryExecutor;

require_once '..\vendor\autoload.php';

// getting mapping file content
$yamlMapping = file_get_contents('emplyees_mapping.yaml');

// instanciate the parser
$parser = new YamlMappingParser();

// parsing mapping
$resource = $parser->parse($yamlMapping);

// instanciate a query builder
$dbalQueryBuilder = new DBALQueryBuilder();

// create a database configuration
$databaseConf = new DatabaseConfiguration();
$databaseConf->setDatabaseName('supercompany_db');
$databaseConf->setDriver('pdo_mysql');
$databaseConf->setHost('127.0.0.1');
$databaseConf->setPassword('SuperSecret!');
$databaseConf->setPort(3306);
$databaseConf->setUsername('myUser');

// instanciate the query executor with the related db config
$queryExecutor = new DBALQueryExecutor($databaseConf);

// instanciate a data extator with the query builder and the query executor 
$dataExtractor = new DataExtractor($dbalQueryBuilder, $queryExecutor);

// extracting data as plain array
$plainData = $dataExtractor->extractData($resource);

// instanciate the array hydrator with the appropriate field hydrator factory
$dataHydrator = new ArrayHydrator(new FieldHydrationConcreteFactory());

// hydrate into an array all the plain data
$hydratedData = $dataHydrator->hydrate($plainData, $resource);

// reformat the data (hydrator will hydrate to objects like \DateTime...)
$dataFormatter = new ArrayFormatter(new FieldFormatterConcreteFactory());
$formattedData = $dataFormatter->format($hydratedData, $resource);

// now you can encode in json
$json = json_encode($formattedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

```

### With facade (more easy)

A proper facade to manage all the process end-to-end will be soon available
