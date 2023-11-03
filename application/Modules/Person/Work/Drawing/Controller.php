<?php namespace Modules\Person\Work\Drawing;

/**
 * General controller for index page
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
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
            ->bindExtension(\Defines\Extension::JSON)
            ->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
            ->bindNullKey();
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        array_unshift($aParams, 'work');
        $this->request->setParams($aParams);

        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);
        $oHelper->add('index');
        return $oHelper;
    }
}
