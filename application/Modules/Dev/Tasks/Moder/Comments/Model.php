<?php namespace Modules\Dev\Tasks\Moder\Comments;

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
    public function getList($sLanguage)
    {
        return (new \Data\ContentHelper)->getEntityManager()->createQuery(
                "SELECT c
                FROM Data\Doctrine\Main\Content c
                WHERE
                    c.language = :language
                    AND c.pattern LIKE :pattern
                    AND c.type LIKE :type
                    AND (c.access LIKE :access OR c.access LIKE :access2)
                ORDER BY c.updatedAt DESC"
            )
            ->setParameter('language', $sLanguage)
            ->setParameter('type', 'content#%')
            ->setParameter('pattern', 'dev/%/i%')
            ->setParameter('access', '_' . \Defines\User\Access::MODERATE . '_')
            ->setParameter('access2', '__' . \Defines\User\Access::MODERATE)
            ->setMaxResults(\Defines\Database\Params::COMMENTS_ON_PAGE)
            ->getResult();
    }

    /**
     * Change access status for comment
     *
     * @param integer $id
     * @param string $sStatus
     * @param boolean $bUser
     */
    public function changeStatus($id, $sStatus, $bUser = false)
    {
        $oHelper = new \Data\ContentHelper();
        /* @var $oData \Data\Doctrine\Main\Content */
        $oData = $oHelper->getRepository()->find($id);
        if ($oData) {
            $oData->setAccess($sStatus);
            if ($bUser) {
                $oData->setAuditor(\System\Registry::user()->getEntity());
            }
            $oHelper->getEntityManager()->persist($oData);
            $oHelper->getEntityManager()->flush();
        }
    }
}
