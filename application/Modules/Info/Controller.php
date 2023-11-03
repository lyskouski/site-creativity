<?php namespace Modules\Info;

/**
 * General controller for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/About
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
                ->bindNullKey();

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function indexAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);
        $oHelper->add('index', array(
            'workflow_tmp' => '../../Dev/Proposition/zView/stat',
            'workflow' => (new \Modules\Dev\Proposition\Model)->getWorkflow()
        ));
        return $oHelper;
    }

    public function policyAction($aParams)
    {
        return $this->getSection($aParams, 'policy');
    }

    public function termsAction($aParams)
    {
        return $this->getSection($aParams, 'terms');
    }

    public function partnersAction($aParams)
    {
        return $this->getSection($aParams, 'partners');
    }

    public function authorsAction($aParams)
    {
        return $this->getSection($aParams, 'authors');
    }
}
