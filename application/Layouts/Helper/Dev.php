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
class Dev extends Basic
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

        $aList = array();
        // User tasks
        if (Registry::user()->checkAccess('dev/tasks', 'index')) {
            $aList['tasks'] = array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_SITE_TASKS'),
                'href' => $oTemplate->getUrl('dev/tasks', $sType, $sLanguage)
            );
        }
        // Development tasks
        if (Registry::user()->checkAccess('dev/board', 'index')) {
            $aList['board'] = array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_SITE_BOARD'),
                'href' => $oTemplate->getUrl('dev/board', $sType, $sLanguage)
            );
        }
        // User privilegues
        if (Registry::user()->checkAccess('dev/group', 'index')) {
            $aList['group'] = array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_SITE_GROUP'),
                'href' => $oTemplate->getUrl('dev/group', $sType, $sLanguage)
            );
        }
        // Rollout process
        if (Registry::user()->checkAccess('dev/rollout', 'index')) {
            $aList['rollout'] = array(
                'class' => 'width_auto',
                'title' => Registry::translation()->sys('LB_SITE_ROLLOUT'),
                'href' => $oTemplate->getUrl('dev/rollout', $sType, $sLanguage)
            );
        }

        if (!$aList) {
            $aList = parent::getTopNatigation();
        }

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
