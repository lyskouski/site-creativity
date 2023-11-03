<?php namespace Engine\Response\Helper;

use Engine\Request\Input;

/**
 * Parse url params from request
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Helper
 */
class Url
{

    /**
     * @var \Engine\Request\Input
     */
    protected $oInput;

    /**
     * @var \Engine\Request\Params
     */
    protected $oParams;

    /**
     * Init URL data
     *
     * @param string $sExtra
     */
    public function __construct(\Engine\Request\Params $oParams)
    {
        $this->oInput = new Input();
        $this->oParams = $oParams;
    }

    /**
     * Get actual URL
     *
     * @param string $sLang - language
     * @param boolean $bParams - include url params
     * @param boolean $bRequest - include GET params
     * @return string
     */
    public function getUrl($sLang = null, $bParams = true, $bRequest = true)
    {
        $sController = trim(strtolower(
            str_replace(
                array(\System\Registry::config()->getModulePrefix(), '\\'),
                array('', '/'),
                $this->oParams->getModuleName()
            )
        ), '/');

        $aGetParams = array();
        if ($bParams) {
            foreach ($this->oParams->getParams() as $i => $s) {
                if (is_integer($i)) {
                    $sController .= "/{$s}";
                } else {
                    $aGetParams[$i] = $s;
                }
            }
        }

        $url = \System\Registry::config()->getUrl($sLang);

        $currentUrl = "{$url}/{$sController}.{$this->oParams->getResponseType()}";
        if ($bRequest && $aGetParams) {
            $currentUrl .= '?' . http_build_query($aGetParams);
        }
        return $currentUrl;
    }
}
