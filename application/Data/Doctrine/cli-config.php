<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require __DIR__ . '/../../../vendor/autoload.php';

$aDbParams = array(
    'driver' => '',
    'user' => '',
    'password' => '',
    'dbname' => '',
);

$oConfig = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/Main/'), true, null, null, false);
// $oConfig = Setup::createXMLMetadataConfiguration(array(__DIR__ . '/Xml/'), true, null, null, false);
$oConfig->setProxyDir( __DIR__ . '/Proxy');
$oConfig->setProxyNamespace("Data\\Doctrine\\Proxy\\");
$oConfig->setAutoGenerateProxyClasses(true);

$oEntityManager = EntityManager::create($aDbParams, $oConfig);

// we treat the enums as strings
$platform = $oEntityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

// This fix can be applied to any unsupported data type, for example SET (which is also used in PyroCMS):
$platform->registerDoctrineTypeMapping('set', 'string');

// With this expression all tables prefixed with t_ will ignored by the schema tool.
// $oConfig->setFilterSchemaAssetsExpression("~^(?!(Delete_Documents|Documents_Versions|Importer_Params|Print_Securities_*|Tradebox_Document_Cache|disclaimer_mm|fetchlog|log_tmp|task_*))~");

// Only one table
// $oConfig->setFilterSchemaAssetsExpression("~^(translations)$~");

return ConsoleRunner::createHelperSet($oEntityManager);

// ======= HOW TO =========
// step 0: cd __DIR__
// step 1: doctrine orm:convert-mapping --from-database --force xml ./Xml
// step 2: doctrine orm:convert-mapping --namespace="Entity\\" --force annotation --from-database ./
// step 2.1: doctrine orm:generate-entities --generate-annotations true --regenerate-entities true ./Entity/
// step 3: mv __DIR__/Entity to __DIR__/../Entity
// step 4: doctrine orm:generate-proxies