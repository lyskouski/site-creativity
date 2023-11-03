<?php namespace Modules\Dev\Tasks\Moder\Topics;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\Dev\Tasks\Model
{

    /**
     * Get task
     *
     * @param string $sLanguage
     * @return \Data\Doctrine\Main\Content
     * @throws \Error\Validation
     */
    public function getTopic($sLanguage)
    {
        $oHelper = new \Data\ContentHelper();

        /* @var $oQuery \Doctrine\ORM\QueryBuilder*/
        $oQuery = $oHelper->getEntityManager()->createQueryBuilder();
        $oQuery->select('min(c.id)')
            ->from(\Defines\Database\CrMain::CONTENT, 'c')
            ->where('c.auditor is null', 'c.language = :language', 'c.type = :type', 'c.pattern LIKE :pattern')
            ->setParameters(array(
                'type' => \Defines\Content\Attribute::TYPE_KEYS,
                'language' => $sLanguage,
                'pattern' => 'dev/%/i%'
            ));

        $oResult = $oHelper->getRepository()->find($oQuery->getQuery()->getSingleScalarResult());
        if (!$oResult) {
            throw new \Error\Validation( \System\Registry::translation()->sys('LB_TASK_IS_MISSING') );
        }

        $oResult->setAuditor(\System\Registry::user()->getEntity());
        $oHelper->getEntityManager()->persist($oResult);
        $oHelper->getEntityManager()->flush();

        $oTitle = $oHelper->getRepository()->findOneBy(array(
            'pattern' => $oResult->getPattern(),
            'language' => $oResult->getLanguage(),
            'type' => \Defines\Content\Attribute::TYPE_TITLE,
        ));

        return $oTitle;
    }
}
