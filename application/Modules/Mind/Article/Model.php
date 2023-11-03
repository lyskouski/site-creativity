<?php namespace Modules\Mind\Article;

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
    protected $iLimit = \Defines\Database\Params::CONTENT_ON_PAGE;

    /**
     * @var \Data\Model\ContentRepository
     */
    protected $oRep;

    public function __construct()
    {
        $this->oRep = (new \Data\ContentHelper)->getRepository();
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
        return $this->oRep->getPages('mind/article');

    }

    /**
     * Get publications on the current page
     *
     * @param integer $iPage
     * @return array
     */
    public function getPublications($iPage = 0)
    {
        return $this->oRep->findLastTopics('mind/%', $this->iLimit * $iPage, $this->iLimit);
    }

    public function getPublicationsByKey($sKey, $iPage = 0) {
        return $this->oRep->findTopicsByKey('mind/%', $sKey, $this->iLimit * $iPage, $this->iLimit);
    }

    public function getPublicationsByPart($sKey, $iPage = 0) {
        return $this->oRep->findTopicsByKey('mind/%', $sKey, $this->iLimit * $iPage, $this->iLimit, ['keywords', 'og:title', 'description']);
    }
}
