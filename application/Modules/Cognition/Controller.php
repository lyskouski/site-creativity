<?php namespace Modules\Cognition;

/**
 * Ouvre controller
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML);
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('cognition'));
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);

        $oHelper->add('index');
        return $oHelper;
    }

}
