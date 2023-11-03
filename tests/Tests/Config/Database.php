<?php namespace Tests\Config;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use System\Registry;

/**
 * Basic functionality :memory: database testing
 *
 * @author Viachaslau Lyskouski
 */
class Database
{
    const REG_TYPE = 'db.test';

    protected $entities = array();

    /**
     * @fixme be aware of the current option \Tests\Database::getRealDatabase
     * @return type
     */
    public function getRealDatabase()
    {
        $conn = $this->getCustomEntityManager('pdo-mysql://---?charset=utf8');
        return $conn;
    }

    /**
     * Get customized database connection
     *
     * @param string $sDbUrl
     * @return \Doctrine\ORM\EntityManager
     */
    public function getCustomEntityManager($sDbUrl)
    {
        $aParams = array(
            'url' => $sDbUrl,
        );
        $bDevMode = true;
        $aPaths = array(Registry::config()->getAppPath());
        //$ormConfig = Setup::createYAMLMetadataConfiguration( $paths, $isDevMode, null, null, false );
        $ormConfig = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($aPaths, $bDevMode, null, null, false);
        //$ormConfig = Setup::createXMLMetadataConfiguration( $paths, $isDevMode, null, null, false );

        return \Doctrine\ORM\EntityManager::create($aParams, $ormConfig);
    }

    /**
     * Create database in memory
     *
     * @return type
     * @throws Exception
     */
    public function initConnection()
    {
        if (!(new \Engine\Request\Input\Server)->isTest()) {
            throw new \Exception('Cannot be done! Is used only for tests...');
        }

        // Get an instance of your entity manager
        $oDatabase = new \System\Database\Connector(\Defines\Connector::MYSQL_DOCTRINE);
        $entityManager = $oDatabase->getConnection();

        // Clear Doctrine to be safe
        $entityManager->clear();


        /* @var $driver \Doctrine\ORM\Mapping\Driver\AnnotationDriver */
        $driver = $entityManager->getConfiguration()->getMetadataDriverImpl();
        $oTargetDriver = new AnnotationDriver($driver->getReader(), array(Registry::config()->getAppPath() . '../Data/Doctrine/Main'));

        $classes = $oTargetDriver->getAllClassNames();
        $this->entities = array();
        $o = new \Doctrine\ORM\Mapping\ClassMetadataFactory();
        $o->setEntityManager($entityManager);
        foreach ($classes as $class) {
            $metadata = $o->getMetadataFor($class);
            $this->entities[] = $metadata;
        }

        $this->revalidate($entityManager);

        // Change connector
        return $entityManager;
    }

    public function revalidate($entityManager)
    {
        // Schema Tool to process our entities
        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);

        // Drop all classes and re-build them for each test case
        $tool->dropSchema($this->entities);
        $tool->createSchema($this->entities);
    }
}
