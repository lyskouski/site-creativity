<?php namespace System\Database;

/**
 * Class to operate with Database
 *
 * @since 2015-08-18
 * @author Viachaslau Lyskouski
 * @package Database
 */
class Connector
{

    /**
     * @var \PDO
     */
    protected $oDatabase;

    /**
     * Start database connection
     *
     * @param string $sType - database type from \Defines\Connector
     * @throws \Error\Application
     */
    public function __construct($sType, $dbName = 'cr_main')
    {
        $oConfig = \System\Registry::config();
        $aPdo = $oConfig->getPdo();
        switch ($sType)
        {
            // connection with Doctrine ORM
            case \Defines\Connector::MYSQL_DOCTRINE:
                $aParams = array(
                    'url' => $aPdo[\Defines\Connector::MYSQL_DOCTRINE],
                );
                $bDevMode = $oConfig->getDevMode();
                $aPaths = array($oConfig->getAppPath());
                //$sCache = $oConfig->getAppPath() . '../Data/Doctrine/Cache';
                $proxyPath = $oConfig->getAppPath() . '../Data/Doctrine/Proxy';

                $ormConfig = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($aPaths, $bDevMode, $proxyPath, null, false);

                // Create proxies
                $ormConfig->setProxyDir($proxyPath);
                $ormConfig->setProxyNamespace('Data\Doctrine\Proxy');
                $ormConfig->setAutoGenerateProxyClasses($bDevMode);

                $oDatabase = \Doctrine\ORM\EntityManager::create($aParams, $ormConfig);
                $oDatabase->getConnection()->getConfiguration()->setSQLLogger(\System\Registry::logger());

                // we treat the enums as strings
                $platform = $oDatabase->getConnection()->getDatabasePlatform();
                $platform->registerDoctrineTypeMapping('enum', 'string');
                $platform->registerDoctrineTypeMapping('set', 'string');
                break;

            // connection with basic PHP functionality
            case \Defines\Connector::MYSQL:
                $oDatabase = new \PDO(
                    'mysql:host='.  $aPdo[\Defines\Connector::MYSQL]['host'] .';dbname='. $dbName,
                    $aPdo[\Defines\Connector::MYSQL]['user'],
                    $aPdo[\Defines\Connector::MYSQL]['password']
                );
                break;

            default:
                throw new \Error\Application( 'Missing Database type' );
        }

        $this->oDatabase = $oDatabase;
    }

    /**
     * Get instance
     *
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->oDatabase;
    }
}
