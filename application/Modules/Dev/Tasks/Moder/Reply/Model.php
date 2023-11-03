<?php namespace Modules\Dev\Tasks\Moder\Reply;

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
                    AND c.auditor IS NULL
                    AND c.type LIKE :type
                ORDER BY c.updatedAt DESC"
            )
            ->setParameter('language', $sLanguage)
            ->setParameter('type', 'comment#%')
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
    public function changeStatus($id, $sStatus)
    {
        $oHelper = new \Data\ContentHelper();
        /* @var $oData \Data\Doctrine\Main\Content */
        $oData = $oHelper->getRepository()->find($id);
        if ($oData) {
            $oData->setAccess($sStatus)
                ->setAuditor(\System\Registry::user()->getEntity());
            $oHelper->getEntityManager()->persist($oData);
            $oHelper->getEntityManager()->flush();
        }
    }


    public function updateComment($id, $content = null)
    {
        $list = $this->getList(\System\Registry::translation()->getTargetLanguage());
        $missing = true;
        foreach ($list as $o) {
            if ($o->getId() == $id) {
                $missing = false;
                break;
            }
        }
        if ($missing) {
            throw new \Error\Validation(\System\Registry::translation()->sys('LB_HEADER_203'));
        }

        $oHelper = new \Data\ContentHelper();
        /* @var $oData \Data\Doctrine\Main\Content */
        $oData = $oHelper->getRepository()->find($id);
        if (!$oData) {
            throw new \Error\Validation(\System\Registry::translation()->sys('LB_HEADER_404'));
        }
        if ($content) {
            $oContent = new \System\Converter\Content($content);
            $oData->setContent($oContent->getHtml())
                ->setSearch($oContent->getText())
                ->setAccess(\Defines\User\Access::getModApprove())
                ->setAuditor(\System\Registry::user()->getEntity());
            $oHelper->getEntityManager()->persist($oData);
            $oHelper->getEntityManager()->flush();
        }
        return $oData;
    }
}
