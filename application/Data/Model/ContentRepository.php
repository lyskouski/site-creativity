<?php namespace Data\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Defines\Database\CrMain;
use Defines\Content\Attribute;
use Access\Request\Search;

/**
 * Helper for a content view
 *
 * @since 2015-11-16
 * @author Viachaslau Lyskouski
 */
class ContentRepository extends EntityRepository
{

    protected $sortKey = 'c.updatedAt';
    protected $sortDir = 'DESC';
    protected $joinCondition = '';

    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        $request = new \Engine\Request\Input();

        $book = 'LEFT JOIN Data\Doctrine\Main\Content AS c2 WITH c2.pattern = c.pattern
                 LEFT JOIN Data\Doctrine\Main\Book AS b WITH b.content = c2.id';
        // Sort option
        switch ($request->getPost(Search::SORT)) {
            case Search::SORT_RATING:
                $this->sortKey = 'cv.votesUp';//, cv.votesDown ASC';
                break;
            case Search::SORT_VIEW:
                $this->sortKey = 'cv.contentCount';
                break;
            // !? has to be applied only for book/overview
            case Search::SORT_BOOK_AUHOR:
                $this->joinCondition = $book;
                $this->sortKey = 'b.author';
                break;
            case Search::SORT_BOOK_TITLE:
                $this->joinCondition = $book;
                $this->sortKey = 'b.title';
                break;
            case Search::SORT_BOOK_DATE:
                $this->joinCondition = $book;
                $this->sortKey = 'b.year';
                break;
            default:
                $this->sortKey = 'c.updatedAt';
        }
        // Sort direction
        if ($request->getPost(Search::SORT_TYPE, false)) {
            $this->sortDir = 'ASC';
        }

        parent::__construct($em, $class);
    }

    public function  findTopicsByKey($sPattern, $sKey, $firstResult, $maxResults, $aType = ['keywords'])
    {
        $query = $this->getEntityManager()->createQuery(
                "SELECT c, {$this->sortKey} AS HIDDEN sortOption
                FROM Data\Doctrine\Main\Content c
                LEFT JOIN Data\Doctrine\Main\ContentViews AS cv WITH cv.content = c.id
                {$this->joinCondition}
                WHERE
                    c.type IN (:type)
                    AND c.language = :language
                    AND c.pattern LIKE :pattern
                    AND c.pattern NOT LIKE :folder
                ".($sKey ? "AND SUBSTRING(c.search, LOCATE('@', c.search)+1) LIKE :search" : '')."
                GROUP BY c.pattern
                ORDER BY sortOption {$this->sortDir}"
            )
            ->setParameter('language', \System\Registry::translation()->getTargetLanguage())
            ->setParameter('type', $aType)
            ->setParameter('pattern', "$sPattern/i%")
            ->setParameter('folder', "%/search/%")
            ->setMaxResults($maxResults)
            ->setFirstResult($firstResult);

        if ($sKey) {
            $query->setParameter('search', "%$sKey%");
        }

        /* @var $oContent \Data\Doctrine\Main\Content */
        $aPattern = array();
        foreach ($query->getResult() as $oContent) {
            $aPattern[] = $oContent->getPattern();
        }

        $titleQuery = $this->getEntityManager()->createQuery(
                "SELECT c, {$this->sortKey} AS HIDDEN sortOption
                FROM Data\Doctrine\Main\Content c
                LEFT JOIN Data\Doctrine\Main\ContentViews AS cv WITH cv.content = c.id
                {$this->joinCondition}
                WHERE
                    c.type = :type
                    AND c.language = :language
                    AND c.pattern IN (:pattern)
                ORDER BY sortOption {$this->sortDir}"
            )
            ->setParameter('type', Attribute::TYPE_TITLE)
            ->setParameter('language', \System\Registry::translation()->getTargetLanguage())
            ->setParameter('pattern', $aPattern)
            ->setMaxResults($maxResults)
            ->setFirstResult(0);
        $aResult = $this->prepareData($titleQuery->getResult());

        $pagination = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $aResult['count'] = $pagination->count();

        return $aResult;
    }

    /**
     * Get last comments
     *
     * @param string $sPattern
     * @param integer $iPage
     * @param integer $maxResults
     * @return array<\Data\Doctrine\Main\Content>
     */
    public function findComments($sPattern, $iPage = 0, $maxResults = \Defines\Database\Params::COMMENTS_ON_PAGE)
    {
        return $this->_em->createQuery(
                "SELECT c
                FROM Data\Doctrine\Main\Content c
                WHERE
                    c.language = :language
                    AND c.pattern = :pattern
                    AND c.type LIKE :type
                ORDER BY c.updatedAt DESC"
            )
            ->setParameter('language', \System\Registry::translation()->getTargetLanguage())
            ->setParameter('type', 'comment#%')
            ->setParameter('pattern', $sPattern)
            ->setFirstResult($iPage * $maxResults)
            ->setMaxResults($maxResults)
            ->getResult();
    }

    /**
     *
     * @param string $sPattern
     * @param string $sContent
     * @param string $sMark
     */
    public function addComment($sPattern, $sContent, $sMark)
    {
        $oTranslate = \System\Registry::translation();
        $oUser = \System\Registry::user()->getEntity();
        if (!$oUser) {
            throw new \Error\Validation($oTranslate->sys('LB_ERROR_NOT_AUTHORIZED'));
        }

        try {
            $this->_em->beginTransaction();

            $oStat = \System\Registry::stat();
            $sKey = 'comment#';
            switch ($sMark) {
                case 'votes_up':
                    $sKey .= $oUser->getId() . '-up';
                    $oStat->setVotesUp(1 + $oStat->getVotesUp());
                    $this->_em->persist($oStat);
                    break;

                case 'votes_down':
                    $sKey .= $oUser->getId() . '-down';
                    $oStat->setVotesDown(1 + $oStat->getVotesDown());
                    $this->_em->persist($oStat);
                    break;

                default:
                    $sKey .= $oUser->getId() . '-ntr';
                    $cnt = $this->_em->createQuery(
                        "SELECT COUNT(c.id)
                        FROM Data\Doctrine\Main\Content c
                        WHERE
                            c.language = :language
                            AND c.pattern = :pattern
                            AND c.type LIKE :type"
                        )
                        ->setParameter('language', $oTranslate->getTargetLanguage())
                        ->setParameter('pattern', $oStat->getContent()->getPattern())
                        ->setParameter('type', "$sKey%")
                        ->getSingleScalarResult();
                    $sKey .= $cnt;
            }

            $access = \Defines\User\Access::getModerate();
            if (\System\Registry::user()->checkAccess('dev/tasks')) {
                $access = \Defines\User\Access::getModApprove();
            }

            $content = new \System\Converter\Content($sContent);

            $oComment = new \Data\Doctrine\Main\Content();
            $oComment->setAuthor($oUser)
                ->setContent($content->getHtml(true))
                ->setSearch($content->getText())
                ->setPattern($oStat->getContent()->getPattern())
                ->setLanguage($oTranslate->getTargetLanguage())
                ->setType($sKey)
                ->setUpdatedAt(new \DateTime)
                ->setAccess($access);
            $this->_em->persist($oComment);

            $this->_em->flush();
            $this->_em->commit();
        } catch (\Exception $e) {
            $this->_em->rollback();
            throw new \Error\Validation($oTranslate->sys('LB_ERROR_DUPLICATED_VOTE'));
        }
    }

    /**
     * Find the newest topics from the current category
     *
     * @param string $sPattern - pattern match '{value}/%/i%'
     * @param integer $firstResult - first result
     * @param integer $maxResults - max number of results
     * @param boolean $prepare - check if prepareData is needed
     * @return array - ['og:title' => array<\Data\Doctrine\Main\Content>, 'description' => array<string>, 'og:image' => array<string>, 'image' => array<string>]
     */
    public function findLastTopics($sPattern, $firstResult, $maxResults, $prepare = true)
    {
        $aData = $this->_em->createQuery(
                "SELECT c, {$this->sortKey} AS HIDDEN sortOption
                FROM Data\Doctrine\Main\Content c
                LEFT JOIN Data\Doctrine\Main\ContentViews AS cv WITH cv.content = c.id
                WHERE
                    c.type = :type
                    AND c.language = :language
                    AND c.pattern LIKE :pattern AND c.pattern NOT LIKE :search
                ORDER BY sortOption {$this->sortDir}"
            )
            ->setParameter('language', \System\Registry::translation()->getTargetLanguage())
            ->setParameter('type', Attribute::TYPE_TITLE)
            ->setParameter('pattern', "$sPattern/i%")
            ->setParameter('search', '%/search/%')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->getResult();

        if ($prepare) {
            $aData = $this->prepareData($aData);
        }
        return $aData;
    }

    /**
     * Compile all needed values
     *
     * @param array<\Data\Doctrine\Main\Content> $aData
     * @return array
     */
    public function prepareData($aData)
    {
        $oTranslate = \System\Registry::translation();
        $imgPath = new \System\Minify\Images();

        $aReturn = array(
            Attribute::TYPE_TITLE => array(),
            Attribute::TYPE_KEYS => array(),
            Attribute::TYPE_DESC => array(),
            Attribute::TYPE_IMG => array(),
            'image' => array()
        );
        /* @var $oContent \Data\Doctrine\Main\Content */
        foreach ($aData as $oContent) {
            $s = $oContent->getPattern();
            $aReturn[Attribute::TYPE_TITLE][$s] = $oContent;
            $aReturn[Attribute::TYPE_KEYS][$s] = $oTranslate->get([Attribute::TYPE_KEYS, $s], $oContent->getLanguage());
            $aReturn[Attribute::TYPE_DESC][$s] = $oTranslate->get([Attribute::TYPE_DESC, $s], $oContent->getLanguage(), function($data) {
                if (!$data || $data[0] === '{') {
                    $data = null;
                }
                return $data;
            });
            // Fill images
            $aReturn[Attribute::TYPE_IMG][$s] = $oTranslate->get([Attribute::TYPE_IMG, $s], $oContent->getLanguage(), $imgPath->adaptWork($s));
            // Fill images types
            $aReturn['image'][$s] = $oTranslate->get(['image', $s], $oContent->getLanguage(), $imgPath->adaptWork($s, '_type'));
        }
        return $aReturn;
    }

    /**
     * Return statistical entity
     *
     * @param string $sPattern
     * @return \Data\Doctrine\Main\ContentViews
     */
    public function getPages($sPattern)
    {
        $oContent = $this->_em->getRepository(CrMain::CONTENT)->findOneBy(array(
            'pattern' => $sPattern,
            'type' => Attribute::TYPE_TITLE,
            'language' => \System\Registry::translation()->getTargetLanguage()
        ));

        $oCounter = null;
        if ($oContent) {
            $oCounter = $this->_em->getRepository(CrMain::CONTENT_VIEWS)->find($oContent->getId());
        }

        if (!$oCounter) {
            $oCounter = new \Data\Doctrine\Main\ContentViews();
        }
        return $oCounter;
    }

    /**
     * Get list of languages for the content
     *
     * @param string $sUrl
     * @return array
     */
    public function getContentLanguages($sUrl)
    {
        $a = $this->_em->getRepository(CrMain::CONTENT)->findBy(array(
                'pattern' => $sUrl,
                'type' => 'og:title'
            ), array(
                'language' => 'ASC'
            )
        );
        $aLanguages = array();
        /* @var $o \Data\Doctrine\Main\Content */
        foreach ($a as $o) {
            $aLanguages[$o->getId()] = $o->getLanguage();
        }
        return $aLanguages;
    }
}
