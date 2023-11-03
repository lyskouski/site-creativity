<?php namespace Layouts\Helper;

use System\Registry;
use Engine\Request\Input;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Layouts/Helper
 */
class Login extends Basic
{

    /**
     * Get auth and configuration buttons
     *
     * @return array
     */
    protected function getTopButtons()
    {
        return array(
            array(
                'type' => 'attention',
                'title' => Registry::translation()->sys('LB_RETURN'),
                'href' => (new Input)->getRefererUrl()
            )
        );
    }

    /**
     * Get TOP navigation
     *
     * @return array
     */
    public function getTopNatigation()
    {
        $aAsync = array(
            'class' => 'Request/Pjax',
            'actions' => 'init'
        );

        $aList = array(
            /*
            'mail' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_AUTH_MAIL'),
                'href' => $this->updateUrl('/in#!/mail'),
                'data' => $aAsync
            ),
            'vk' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_AUTH_VK'),
                'href' => $this->updateUrl('/in#!/vk'),
                'data' => $aAsync
            ),
            'facebook' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_AUTH_FACEBOOK'),
                'href' => $this->updateUrl('/in#!/facebook'),
                'data' => $aAsync
            ),
            'linkedin' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_AUTH_LINKEDIN'),
                'href' => $this->updateUrl('/in#!/linkedin'),
                'data' => $aAsync
            ),*/
            /*'twitter' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_AUTH_TWITTER'),
                'href' => $this->updateUrl('/in#!/twitter'),
                'data' => $aAsync
            ),*/
            /*'google' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_AUTH_GOOGLE'),
                'href' => $this->updateUrl('/in#!/google'),
                'data' => $aAsync
            ),*/
            /*'persona' => array(
                'class' => 'width_auto ui',
                'title' => Registry::translation()->sys('LB_AUTH_PERSONA'),
                'href' => $this->updateUrl('/in#!/persona'),
                'data' => $aAsync
            )*/
        );

        //$sTargetAuth = (new \System\Aggregator)->getValue($this->params->getParams(), 1, 'mail');
        //$aList[$sTargetAuth]['class'] .= ' active';
        return $aList;
    }
}
