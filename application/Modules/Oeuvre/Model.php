<?php namespace Modules\Oeuvre;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    /**
     * Output limitation
     * @var integer
     */
    protected $iLimit = 20;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Data\Model\ContentRepository
     */
    protected $rep;

    public function __construct()
    {
        $this->em = \System\Registry::connection();
        $this->rep = $this->em->getRepository(\Defines\Database\CrMain::CONTENT);
    }

    /**
     * Get number of elements per page
     *
     * @return integer
     */
    public function getPageCount()
    {
        return $this->iLimit;
    }

    /**
     * Get summary statistic information
     *
     * @return \Data\Doctrine\Main\ContentViews
     */
    public function getStatistics()
    {
        return $this->rep->getPages('oeuvre');

    }

    /**
     * Get publications on the current page
     *
     * @param integer $iPage
     * @return array
     */
    public function getPublications($iPage = 0)
    {
        return $this->rep->findLastTopics('oeuvre/%', $this->iLimit * $iPage, $this->iLimit);
    }

    public function getPublicationsByKey($sKey, $iPage = 0) {
        return $this->rep->findTopicsByKey('oeuvre/%', $sKey, $this->iLimit * $iPage, $this->iLimit);
    }

    public function getPublicationsByPart($sKey, $iPage = 0) {
        return $this->rep->findTopicsByKey('oeuvre/%', $sKey, $this->iLimit * $iPage, $this->iLimit, ['keywords', 'og:title', 'description']);
    }
}
