<?php namespace Modules\About;

use Defines\Database\CrMain;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    public function checkDescription()
    {
        $oTranslate = \System\Registry::translation();
        $search = array(
            'pattern' => 'about/error',
            'type' => 'content#0',
            'language' => $oTranslate->getTargetLanguage()
        );

        $oExist = \System\Registry::connection()->getRepository(CrMain::CONTENT)->findOneBy($search);
        if (!$oExist) {
            $oExist = \System\Registry::connection()->getRepository(CrMain::CONTENT_NEW)->findOneBy($search);
        }
        if (!$oExist) {
            (new \Modules\Dev\Model)->createTransaltion('about/error', $oTranslate->getTargetLanguage());
        }
        return $oExist;
    }
}
