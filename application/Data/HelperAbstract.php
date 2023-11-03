<?php namespace Data;

/**
 * Abstract helper class
 *
 * @author Viachaslau Lyskouski
 * @since 2015-09-21
 * @package Data
 */
abstract class HelperAbstract
{

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $oRepository;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $oEntityManager;

    /**
     * Get target table from the database
     *
     * @return string
     */
    abstract protected function getTarget();


    /**
     * Init Doctrine Manager & Repository for a database access
     */
    public function __construct()
    {
        // // Is used for cache clearing
        //$cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
        //$cacheDriver->deleteAll();

        $this->oEntityManager = \System\Registry::connection(\Defines\Connector::MYSQL_DOCTRINE);
        $this->oRepository = $this->oEntityManager->getRepository( $this->getTarget() );
    }

    /**
     * For update/insert/delete purposes
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->oEntityManager;
    }

    /**
     * For a select customisation
     *
     * @param string $sEntityName
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository( $sEntityName = null)
    {
        $oRep = $this->oRepository;
        if (!is_null($sEntityName)) {
            $oRep = $this->oEntityManager->getRepository($sEntityName);
        }
        return $oRep;
    }
}
