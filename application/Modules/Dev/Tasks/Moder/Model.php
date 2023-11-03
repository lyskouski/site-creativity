<?php namespace Modules\Dev\Tasks\Moder;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\Dev\Tasks\Model
{
    protected function fillByLang() {
        $aResult = array();
        foreach (\Defines\Language::getList() as $s) {
            $aResult[$s] = 0;
        }
        return $aResult;
    }

    /**
     * Get count of topics that wasn't moderated yet
     *
     * @return array - [language => count, ..]
     */
    public function getTopics()
    {
        /* @var $oQuery \Doctrine\ORM\QueryBuilder */
        $oQuery = (new \Data\ContentHelper)->getEntityManager()->createQueryBuilder();
        $oQuery->select('count(c.id) AS cnt, c.language')
            ->from(\Defines\Database\CrMain::CONTENT, 'c')
            ->where('c.auditor is null', 'c.type = :type', 'c.pattern LIKE :pattern')
            ->setParameters(array(
                'type' => \Defines\Content\Attribute::TYPE_KEYS,
                'pattern' => 'dev/%/i%'
            ))
            ->groupBy('c.language');

        $aResult = $this->fillByLang();
        foreach ($oQuery->getQuery()->getArrayResult() as $a) {
            $aResult[$a['language']] = $a['cnt'];
        }
        return $aResult;
    }

    /**
     * Get count of comments in topics that wasn't moderated yet
     */
    public function getComments()
    {
        /* @var $oQuery \Doctrine\ORM\QueryBuilder */
        $oQuery = (new \Data\ContentHelper)->getEntityManager()->createQueryBuilder();
        $oQuery->select('count(c.id) AS cnt, c.language')
            ->from(\Defines\Database\CrMain::CONTENT, 'c')
            ->where('c.type LIKE :type', 'c.pattern LIKE :pattern', '(c.access LIKE :access OR c.access LIKE :access2)')
            ->setParameters(array(
                'type' => 'content#%',
                'pattern' => 'dev/%/i%',
                'access' => '_' . \Defines\User\Access::MODERATE . '_',
                'access2' => '__' . \Defines\User\Access::MODERATE
            ))
            ->groupBy('c.language');

        $aResult = $this->fillByLang();
        foreach ($oQuery->getQuery()->getArrayResult() as $a) {
            $aResult[$a['language']] = $a['cnt'];
        }
        return $aResult;
    }

    /**
     * Get count of comments in topics that wasn't moderated yet
     */
    public function getReply()
    {
        /* @var $oQuery \Doctrine\ORM\QueryBuilder */
        $oQuery = (new \Data\ContentHelper)->getEntityManager()->createQueryBuilder();
        $oQuery->select('count(c.id) AS cnt, c.language')
                ->from(\Defines\Database\CrMain::CONTENT, 'c')
                ->where('c.type LIKE :type', 'c.auditor is null')
                ->setParameters(array(
                    'type' => 'comment#%'
                ))
                ->groupBy('c.language');

        $aResult = $this->fillByLang();
        foreach ($oQuery->getQuery()->getArrayResult() as $a) {
            $aResult[$a['language']] = $a['cnt'];
        }
        return $aResult;
    }

    /**
     * Get count of comments in topics that wasn't moderated yet
     */
    public function getQuote()
    {
        /* @var $oQuery \Doctrine\ORM\QueryBuilder */
        $oQuery = (new \Data\ContentHelper)->getEntityManager()->createQueryBuilder();
        $oQuery->select('count(c.id) AS cnt, c.language')
                ->from(\Defines\Database\CrMain::CONTENT, 'c')
                ->where('c.type = :type', 'c.auditor is null')
                ->setParameters(array(
                    'type' => 'quote'
                ))
                ->groupBy('c.language');

        $aResult = $this->fillByLang();
        foreach ($oQuery->getQuery()->getArrayResult() as $a) {
            $aResult[$a['language']] = $a['cnt'];
        }
        return $aResult;
    }

}
