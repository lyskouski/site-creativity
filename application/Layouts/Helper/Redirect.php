<?php namespace Layouts\Helper;

use Engine\Response\Meta;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Layouts/Helper
 */
class Redirect extends Basic
{

    /**
     * Redirect to url
     *
     * @param string $sUrl
     * @param integer $iDelay
     */
    public function setUrl($sUrl, $iDelay = 0, $sMessage = '')
    {
        $this->response->meta(new Meta\MetaRedirect($iDelay, $sUrl), true);
        $this->response->meta(new Meta\Script('', "setTimeout(function(){window.location.href = '$sUrl';}, $iDelay*100);"), true);
        $this->add('Basic/null', array(
            \Error\TextAbstract::E_CODE => \Defines\Response\Code::E_GOTO,
            \Error\TextAbstract::E_MESSAGE => $sMessage . \System\Registry::translation()->sys('LB_REDIRECT_TO') . ': ' . $sUrl
        ));
        $this->response->header('Refresh', "{$iDelay}; url={$sUrl}");
    }
}
