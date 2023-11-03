<?php

namespace Modules\Person\Stat;

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
                    ->bindKey('/0', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function indexAction(array $aParams)
    {
        return $this->mindAction($aParams);
    }

    /**
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function mindAction(array $aParams)
    {
        $this->request->setParams(array('stat'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);
        $oHelper->add('mind', array(
            'menu' => (new Model)->getMenu(),
            'active' => 'person/stat/mind'
        ));
        return $oHelper;
    }

    /**
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function publicationAction(array $aParams)
    {
        $this->request->setParams(array('stat'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);
        $oHelper->add('publication', array(
            'menu' => (new Model)->getMenu(),
            'active' => 'person/stat/publication'
        ));
        return $oHelper;
    }


}
