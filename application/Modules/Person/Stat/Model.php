<?php namespace Modules\Person\Stat;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    public function getMenu()
    {
        $oTranslate = \System\Registry::translation();
        return array(
            'person/stat/mind' => $oTranslate->sys('LB_STAT_MIND'),
            'person/stat/book' => $oTranslate->sys('LB_STAT_BOOK'),
            'person/stat/publication' => $oTranslate->sys('LB_STAT_PUBLICATION')
        );
    }

    /**
     * Get User publications
     *
     * @param array $aOrder
     */
    public function getPublications($oUser, $aOrder, $iLimit = 7)
    {
        $oHelper = new \Data\ContentHelper();
        return $oHelper->getRepository()->findBy(
            array(
                'author' => $oUser,
                'type' => 'og:title'
            ),
            $aOrder,
            $iLimit
        );
    }

    public function getBookRead($iPage, $iLimit)
    {
        $em = \System\Registry::connection();
        $persister = $em->getUnitOfWork()->getEntityPersister(\Defines\Database\CrMain::BOOK_READ);

        $criteria = array(
            'user' => \System\Registry::user()->getEntity(),
            'status' => [
                \Defines\Database\BookCategory::FINISH,
                \Defines\Database\BookCategory::DELETE
            ]
        );

        return array(
            $persister->loadAll($criteria, ['updatedAt' => 'DESC'], $iLimit, $iPage * $iLimit),
            $persister->count($criteria)
        );
    }

    public function getBookReadById($id)
    {
        $em = \System\Registry::connection();
        /* @var $br \Data\Doctrine\Main\BookRead */
        $br = $em->find(\Defines\Database\CrMain::BOOK_READ, $id);
        if ($br->getUser() !== \System\Registry::user()->getEntity()) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_USER_ACCESS_OWNER'),
                \Defines\Response\Code::E_CONFLICT
            );
        }

        return $br;
    }
}
