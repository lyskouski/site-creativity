<?php namespace Layouts\Helper;

use System\Registry;
use Engine\Response\Template;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Layouts/Helper
 */
class Book extends Basic
{

    /**
     * Get TOP navigation
     *
     * @return array
     */
    public function getTopNatigation()
    {
        $sLanguage = $this->params->getLanguage();
        $sType = $this->params->getResponseType();

        $oTemplate = new Template();

        $aList = array(
            'overview' => array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_BOOK_OVERVIEW'),
                'href' => $oTemplate->getUrl('book/overview', $sType, $sLanguage)
            ),
            'calendar' => array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_BOOK_LIST'),
                'href' => $oTemplate->getUrl('book/calendar', $sType, $sLanguage)
            ),
            'recite' => array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_BOOK_RECITE'),
                'href' => $oTemplate->getUrl('book/recite', $sType, $sLanguage)
            )
        );


        $sTargetAuth = current($this->params->getParams());
        if (isset($aList[$sTargetAuth])) {
            if (!isset($aList[$sTargetAuth]['class'])) {
                $aList[$sTargetAuth]['class'] = '';
            }
            $aList[$sTargetAuth]['class'] .= ' active';
        }
        return $aList;
    }

}
