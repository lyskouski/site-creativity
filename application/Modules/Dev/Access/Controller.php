<?php namespace Modules\Dev\Access;

use Engine\Response\Meta\Script;

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
                ->copyToExtension(\Defines\Extension::JSON);

        if ($this->action === 'saveAction') {
            $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
                ->unbindExtension(\Defines\Extension::HTML);
        }

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Show current state of all permissions and links
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        $oModel = new Model;
        // for a link highlighting
        $this->request->setParams(array('access'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);

        $aParams['list'] = $oModel->getAccessList();
        $aParams['url_list'] = $oModel->getActionList();
        $oHelper->add('index', $aParams);
        return $oHelper;
    }

    /**
     * Save permission changes
     *
     * @param array $aParams
     */
    public function saveAction(array $aParams)
    {
        $aData = $this->input->getPost('access_action', array(), FILTER_VALIDATE_BOOLEAN);
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);
        (new Model)->saveChanges($aData, $this->input->getPost('access', array()));
        $this->response->meta(new Script('', 'window.location.reload();'));
        return $oHelper;
    }
}
