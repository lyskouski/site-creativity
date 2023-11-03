<?php namespace Modules\Log\Auth;

use Engine\Response\Meta\Script;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::JSON)
                ->bindKey('account')
                ->bindKey('token')
                ->bindKey('type', array('list' => \Defines\User\Account::getList()))
                ->copyToExtension(\Defines\Extension::HTML);
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Bind name and profile to and authorised used
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        if (!isset($aParams['account']) || !$aParams['account']) {
            throw new \Error\Validation('Missing account identificator', \Defines\Response\Code::E_FORBIDDEN);
        }
        $oHelper = new \Layouts\Helper\Login($this->request, $this->response);
        $oHelper->add('registry', $aParams);
        return $oHelper;
    }

    /**
     * Final stage of a registry for user
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function acceptAction(array $aParams)
    {
        $aData = array_merge($aParams, $this->input->getPost());
        $oHelper = new \Layouts\Helper\Login($this->request, $this->response);

        $oModel = new Model();
        if ($oModel->linkProfile($aData)) {
            $this->response->meta(new Script('', "window.location = jQuery('.el_top_button')[0].href;"));
        } else {
            $aParams['token'] = '';
            $oHelper->add('registry', array_merge($aParams, $oModel->getError()));
        }
        return $oHelper;
    }

}
