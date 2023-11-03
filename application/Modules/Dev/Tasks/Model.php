<?php

namespace Modules\Dev\Tasks;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel {

    /**
     * Get left navigation panel list
     * @return array
     */
    public function getNavigation() {
        $oTranslate = \System\Registry::translation();
        $aList = array();

        if (\System\Registry::user()->checkAccess('dev/tasks', 'index')) {
            $aList['dev/tasks'] = $oTranslate->sys('LB_ACCESS_NEW_EXPERT');
        }
        if (\System\Registry::user()->checkAccess('dev/tasks/translation', 'index')) {
            $aList['dev/tasks#!/translation'] = $oTranslate->sys('LB_ACCESS_TRANSLATOR');
        }
        if (\System\Registry::user()->checkAccess('dev/tasks/moder', 'index')) {
            $aList['dev/tasks#!/moder'] = $oTranslate->sys('LB_ACCESS_MODER');
        }
        if (\System\Registry::user()->checkAccess('dev/tasks/auditor', 'index')) {
            $aList['dev/tasks#!/auditor'] = $oTranslate->sys('LB_ACCESS_AUDITOR');
        }

        return $aList;
    }

}
