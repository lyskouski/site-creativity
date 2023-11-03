<?php namespace Modules\Book\Recite;

/**
 * Recite Model
 *
 * @since 2016-11-14
 * @author Viachaslau Lyskouski
 */
class Model
{

    public function getMenu()
    {
        $oTranslate = \System\Registry::translation();
        $menu = array();
        if (\System\Registry::user()->isLogged()) {
            $menu['book/recite'] = $oTranslate->sys('LB_BOOK_RECITE_RANDOM');
            $menu['book/recite/mine'] = $oTranslate->sys('LB_BOOK_RECITE_PERSON');
            $menu['book/recite/import'] = $oTranslate->sys('LB_BOOK_RECITE_IMPORT');
        }
        return $menu;
    }

    public function getRandom()
    {
        $em = \System\Registry::connection();
        return $em->getRepository(\Defines\Database\CrMain::CONTENT)
                ->findBy(array(
                    'type' => 'quote',
                    'language' => \System\Registry::translation()->getTargetLanguage(),
                    'access' => \Defines\User\Access::getModApprove()
                    ), ['updatedAt' => 'DESC'], 20);
    }
}
