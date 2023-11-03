<?php namespace Modules\About;

/**
 * About controller
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
            ->bindExtension(\Defines\Extension::HTML)
                ->bindKey('/0', array('ctype' => 'integer'));
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function errorAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);
        $oHelper->add('error', array(
            'content' => (new Model)->checkDescription(),
            'error' => current($aParams)
        ));
        return $oHelper;
    }

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);

        $oHelper->add('index', array(
            'list' => (new \Modules\Dev\Access\Model)->getAccessList(),
        ));

        return $oHelper;
    }
}
