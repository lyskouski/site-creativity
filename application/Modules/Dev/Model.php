<?php namespace Modules\Dev;

use Data\Doctrine\Main\Content;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    /**
     * Get list of topics
     *
     * @param string $sTargetUrl - pattern for content
     * @param integer $iPage
     * @param integer $iSize
     * @return array<\Data\Doctrine\Main\Content>
     */
    public function getList($sTargetUrl, $iPage = 0, $iSize = \Defines\Database\Params::COMMENTS_ON_PAGE)
    {
        $oHelper = new \Data\ContentHelper();
        return $oHelper->getRepository()->createQueryBuilder('o')
            ->where('o.language = :language')
            ->andWhere('o.type = :type')
            ->andWhere('o.pattern LIKE :pattern')
            ->setParameter('language', \System\Registry::translation()->getTargetLanguage())
            ->setParameter('type', 'og:title')
            ->setParameter('pattern', "$sTargetUrl/i%")
            ->addOrderBy('o.updatedAt', 'DESC')
            ->setFirstResult($iPage * $iSize)
            ->setMaxResults($iSize)
            ->getQuery()
            ->getResult();
    }

    public function getByKeys($sTargetUrl, $sWord, $iPage = 0, $iSize = \Defines\Database\Params::COMMENTS_ON_PAGE)
    {
        $oHelper = new \Data\ContentHelper();
        $oQuery = $oHelper->getRepository()->createQueryBuilder('o')
            ->where('o.language = :language')
            ->andWhere('o.type = :type')
            ->andWhere('o.pattern LIKE :pattern')
            ->andWhere('o.search LIKE :search')
            ->setParameter('language', \System\Registry::translation()->getTargetLanguage())
            ->setParameter('type', 'keywords')
            ->setParameter('pattern', "$sTargetUrl/i%")
            ->setParameter('search', "%$sWord%")
            ->addOrderBy('o.updatedAt', 'DESC')
            ->setFirstResult($iPage * $iSize)
            ->setMaxResults($iSize)
            ->getQuery();

        $oResult = new \Doctrine\ORM\Tools\Pagination\Paginator($oQuery);
        $aResult = $oResult->getQuery()->getResult();

        if (!$aResult) {
            throw new \Error\Validation(\System\Registry::translation()->sys('LB_ERROR_INCORRECT_REQUEST'));
        }

        $this->autocreateInfo($sTargetUrl, $sWord);

        return array(
            'list' => $aResult,
            'count' => $oResult->count(),
            'count_page' => $iSize,
        );
    }

    /**
     * Get list of topics
     *
     * @param string $sTargetUrl - pattern for content
     * @param integer $iPage
     * @param integer $iSize
     * @return array<\Data\Doctrine\Main\Content>
     */
    public function getTopicList($sTargetUrl, $iPage = 0, $iSize = \Defines\Database\Params::COMMENTS_ON_PAGE)
    {
        $oHelper = new \Data\ContentHelper();
        $oQuery = $oHelper->getRepository()->createQueryBuilder('o')
            ->where('o.language = :language')
            ->andWhere('o.type LIKE :type')
            ->andWhere('o.pattern LIKE :pattern')
            ->setParameter('language', \System\Registry::translation()->getTargetLanguage())
            ->setParameter('type', 'content#%')
            ->setParameter('pattern', "$sTargetUrl%");

        // Get last page
        if ($iPage === -1) {
            $oQuery->addOrderBy('o.id', 'DESC')
                ->setFirstResult(0);
        } else {
            $oQuery->addOrderBy('o.id', 'ASC')
                ->setFirstResult($iPage * $iSize);
        }

        $aResult = $oQuery->setMaxResults($iSize)
            ->getQuery()
            ->getResult();

        if ($iPage === -1) {
            $aResult = array_reverse($aResult);
        }
        return $aResult;
    }

    /**
     * Save user comment for topic
     *
     * @param string $sTargetUrl
     * @param string $sText
     * @param boolean $bMark
     */
    public function addComment($sTargetUrl, $sText, $bMark = null)
    {
        $oHelper = new \Data\ContentHelper();
        $em = $oHelper->getEntityManager();
        $oTranslate = \System\Registry::translation();
        $sLanguage = $oTranslate->getTargetLanguage();
        $oAuthor = \System\Registry::user()->getEntity();

        $em->beginTransaction();
        /* @var $oTitle Content */
        $oTitle = $oHelper->getRepository()->findOneBy(array(
            'pattern' => $sTargetUrl,
            'language' => $sLanguage,
            'type' => 'og:title'
        ));

        // Check access type
        $type = \Defines\User\Access::COMMENT;
        if ($bMark) {
            $type = \Defines\User\Access::MARK;
        }

        if (!$oTitle) {
            throw new \Error\Validation($oTranslate->sys('LB_ERROR_INCORRECT_REQUEST'));
        } elseif (!(new \Access\Validate\Check)->setType($type)->isAccepted($oTitle)) {
            throw new \Error\Validation($oTranslate->sys('LB_HEADER_423'));
        }

        $oTitle->setUpdatedAt(new \DateTime);
        $oTitle->setAuditor($oAuthor);
        $em->persist($oTitle);

        $oStat = \System\Registry::stat();
        $i = $oStat->getContentCount();
        $oStat->setContentCount(++$i);
        $em->persist($oStat);

        $oContent = new \System\Converter\Content($sText);

        $access = \Defines\User\Access::getModerate();
        if (\System\Registry::user()->checkAccess('dev/tasks')) {
            $access = \Defines\User\Access::getModApprove();
        }

        $oComment = new Content();
        $oComment->setContent($oContent->getHtml(true))
            ->setSearch($oContent->getText())
            ->setLanguage($sLanguage)
            ->setAuthor($oAuthor)
            ->setUpdatedAt(new \DateTime)
            ->setType('content#'.$i)
            ->setAccess($access)
            ->setPattern($sTargetUrl);
        $em->persist($oComment);
        $em->flush();
        $em->commit();

    }

    public function editComment($entity, $sContent, $aAccess)
    {
        $oHelper = new \Data\ContentHelper();
        $em = $oHelper->getEntityManager();
        $sAccess = implode('', $aAccess);

        $entity->setContent($sContent)
            ->setAuditor(\System\Registry::user()->getEntity())
            ->setUpdatedAt(new \DateTime)
            ->setAccess($sAccess);
        $em->persist($entity);
        $em->flush();
        return $entity->getPattern();
    }

    /**
     * Create translation
     *
     * @param string $sUrl
     * @return integer
     */
    public function createTransaltion($sUrl, $sLanguage)
    {
        $oHelper = new \Data\ContentHelper();
        $em = $oHelper->getEntityManager();
        $user = \System\Registry::user()->getEntity();

        $typeList = array('og:title', 'keywords', 'description', 'content#0');
        $pattern = trim($sUrl, '/');
        $list = $oHelper->getRepository()->findBy(array(
            'pattern' => $pattern,
            'type' => $typeList,
            'language' => \System\Registry::translation()->getTargetLanguage()
        ));

        $missings = array_flip($typeList);
        /* @var $o Content */
        foreach ($list as $o) {
            unset($missings[$o->getType()]);
            $entity = new \Data\Doctrine\Main\ContentNew();
            $entity->setContent("{ {$o->getContent()} }")
                ->setLanguage($sLanguage)
                ->setAuthor($user)
                ->setAuditor($user)
                ->setUpdatedAt($o->getUpdatedAt())
                ->setType($o->getType())
                ->setPattern($o->getPattern())
                ->setAccess(\Defines\User\Access::getAccessNew());
            $em->persist($entity);
        }

        foreach (array_keys($missings) as $missingType) {
            $entity = new \Data\Doctrine\Main\ContentNew();
            $entity->setContent("{ $missingType }")
                ->setLanguage($sLanguage)
                ->setAuthor($user)
                ->setAuditor($user)
                ->setUpdatedAt(new \DateTime)
                ->setType($missingType)
                ->setPattern($pattern)
                ->setAccess(\Defines\User\Access::getAccessNew());
            $em->persist($entity);
        }

        $em->flush();

    }

    /**
     * Save topic
     *
     * @param string $sTargetUrl
     * @param string $sText
     * @return string - url to redirect
     */
    public function addTopic($sTargetUrl, $sTitle, $sDesc, $sText)
    {
        $oHelper = new \Data\ContentHelper();
        $em = $oHelper->getEntityManager();
        $sLanguage = \System\Registry::translation()->getTargetLanguage();
        $oAuthor = \System\Registry::user()->getEntity();
        $sAccess = \Defines\User\Access::getNewTopic();

        $em->beginTransaction();

        $oTitle = new Content();
        $oTitle->setContent(strip_tags($sTitle))
            ->setSearch(strip_tags($sTitle))
            ->setLanguage($sLanguage)
            ->setAuthor($oAuthor)
            ->setUpdatedAt(new \DateTime)
            ->setType('og:title')
            ->setPattern("{$sTargetUrl}/i")
            ->setAccess($sAccess);
        $em->persist($oTitle);
        $em->flush();

        $sUrl = "{$sTargetUrl}/i{$oTitle->getId()}";
        $oTitle->setPattern($sUrl);
        $em->persist($oTitle);

        $desc = strip_tags($sDesc);
        $oDesc = new Content();
        $oDesc->setContent($desc)
            ->setSearch($desc)
            ->setLanguage($sLanguage)
            ->setAuthor($oAuthor)
            ->setUpdatedAt(new \DateTime)
            ->setType('description')
            ->setPattern($sUrl)
            ->setAccess($sAccess);
        $em->persist($oDesc);

        $aKeys = explode(' ', str_replace([',','.','!','?'], '', $desc));
        shuffle($aKeys);
        $sKeys = implode(', ', array_slice($aKeys, 1, 5));
        $oKeys = new Content();
        $oKeys->setContent($sKeys)
            ->setSearch($sKeys)
            ->setLanguage($sLanguage)
            ->setAuthor($oAuthor)
            ->setUpdatedAt(new \DateTime)
            ->setType('keywords')
            ->setPattern($sUrl)
            ->setAccess($sAccess);
        $em->persist($oKeys);

        $oContent = new \System\Converter\Content($sText);
        $oComment = new Content();
        $oComment->setContent($oContent->getHtml())
            ->setSearch($oContent->getText())
            ->setLanguage($sLanguage)
            ->setAuthor($oAuthor)
            ->setUpdatedAt(new \DateTime)
            ->setType('content#0')
            ->setPattern($sUrl)
            ->setAccess($sAccess);

        $em->persist($oComment);

        $em->flush();
        $em->commit();
        return $sUrl;
    }

    /**
     * Save topic
     *
     * @param string $sTargetUrl
     * @param string $sTitle
     * @param string $sDesc
     * @param string $sKeywords
     * @param array $aAccess
     */
    public function editTopic($sTargetUrl, $sTitle, $sDesc, $sKeywords, $aAccess, $bSkip = false)
    {
        $oHelper = new \Data\ContentHelper();
        $em = $oHelper->getEntityManager();
        $sAccess = implode('', $aAccess);

        $em->beginTransaction();
        $this->changeContentValue($em, $sTargetUrl, 'og:title', strip_tags($sTitle), $sAccess, $bSkip);
        $this->changeContentValue($em, $sTargetUrl, 'description', strip_tags($sDesc), $sAccess, $bSkip);
        $this->changeContentValue($em, $sTargetUrl, 'keywords', strip_tags($sKeywords), $sAccess, $bSkip);

        if (!$bSkip) {
            $this->addComment(
                $sTargetUrl,
                \System\Registry::translation()->sys('LB_FORUM_TOPIC_MODIFIED_BY')
            );
        }
        $em->flush();
        $em->commit();
        return $sTargetUrl;
    }

    /**
     * Update content
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $sTargetUrl
     * @param string $sType
     * @param string $sContent
     * @param string $sAccess
     */
    protected function changeContentValue($em, $sTargetUrl, $sType, $sContent, $sAccess, $bSkip = false)
    {
        $aSearch = array(
            'pattern' => $sTargetUrl,
            'language' => \System\Registry::translation()->getTargetLanguage(),
            'type' => $sType
        );
        $o = $em->getRepository(\Defines\Database\CrMain::CONTENT)->findOneBy($aSearch);
        $o->setContent($sContent)
            ->setSearch($sContent)
            ->setAuditor(\System\Registry::user()->getEntity())
            ->setAccess($sAccess);
        if (!$bSkip) {
            $o->setUpdatedAt(new \DateTime);
        }
        $em->persist($o);
    }
}
