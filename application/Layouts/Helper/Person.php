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
class Person extends Basic
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

        $aAsync = array(
            'class' => 'Request/Pjax',
            'actions' => 'init'
        );

        $oTemplate = new Template();
        $aList = array(
            'accounts' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_PERSON_ACCOUNTS'),
                'href' => $oTemplate->getUrl('/person#!/accounts'),
                'data' => $aAsync
            ),
            'work' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_PERSONAL'),
                'href' => $oTemplate->getUrl('/person#!/work'),
                'data' => $aAsync
            ),
            'stat' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_PERSON_STATISTICS'),
                'href' => $oTemplate->getUrl('/person#!/stat'),
                'data' => $aAsync
            ),
            '' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_CONFIG'),
                'href' => $oTemplate->getUrl('/person#!'),
                'data' => $aAsync
            ),
        );

        if (Registry::user()->checkAccess('dev/tasks', 'index')) {
            $aList['tasks'] = array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_SITE_TASKS'),
                'href' => $oTemplate->getUrl('dev/tasks', $sType, $sLanguage)
            );
        }

        //$aList['main'] = array(
        //    'class' => 'width_auto',
        //    'title' => Registry::translation()->sys('LB_SITE_RETURN2MAIN'),
        //    'href' => $oTemplate->getUrl('/index', $sType, $sLanguage)
        //);

        $sTargetAuth = current($this->params->getParams());
        if (isset($aList[$sTargetAuth])) {
            $aList[$sTargetAuth]['class'] .= ' active';
        }
        return $aList;
    }
}
