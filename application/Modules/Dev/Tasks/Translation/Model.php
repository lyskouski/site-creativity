<?php namespace Modules\Dev\Tasks\Translation;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\Dev\Tasks\Model
{

    public function getGuiStatus()
    {
        $oGui = new Gui\Model();
        $aResult = array();
        foreach (\Defines\Language::getList() as $sLang) {
            $sContent = file_get_contents($oGui->getUrl($sLang));
            $aResult[$sLang] = array(
                'all' => substr_count($sContent, 'msgid') - 1,
                'missing' => substr_count($sContent, '{{')
            );
        }
        return $aResult;
    }

    public function getTextStatus()
    {
        $oText = new Text\Model();
        $aResult = array();
        foreach (\Defines\Language::getList() as $sLang) {
            $aResult[$sLang] = $oText->getSumTasks($sLang);
        }
        return $aResult;
    }

    public function getBookStatus()
    {
        $oText = new Book\Model();
        $aResult = array();
        foreach (\Defines\Language::getList() as $sLang) {
            $aResult[$sLang] = $oText->getSumTasks($sLang);
        }
        return $aResult;
    }

    public function getTaskStatus()
    {
        $oText = new Task\Model();
        $aResult = array();
        foreach (\Defines\Language::getList() as $sLang) {
            $aResult[$sLang] = $oText->getSumTasks($sLang);
        }
        return $aResult;
    }

}
