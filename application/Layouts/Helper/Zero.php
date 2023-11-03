<?php namespace Layouts\Helper;

/**
 * Fully nullable layout
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Layouts/Helper
 */
class Zero extends Basic
{

    public function __construct(\Engine\Request\Params $oParams, \Engine\Response $oResponse)
    {
        $this->params = $oParams;
        $this->response = $oResponse;
    }

    public function finalize()
    {
        return $this;
    }

    public function add($sTemplate, array $aParams = array(), $sDirPath = null)
    {
        $oTemplate = new \Engine\Response\Template('Basic/null');
        $oTemplate->add($aParams);
        $this->response->push($oTemplate);
        return $this;
    }
}
