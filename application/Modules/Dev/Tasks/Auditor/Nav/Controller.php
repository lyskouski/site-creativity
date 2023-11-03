<?php namespace Modules\Dev\Tasks\Auditor\Nav;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Dev/Tasks
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod( \Defines\RequestMethod::GET )
                ->bindExtension( \Defines\Extension::HTML )
                    ->bindKey('/0', array('type'=>'integer'))
                ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action');

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Show summary information
     * @todo
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexAction(array $aParams)
    {
        // @todo
    }

    /**
     * Task for an auditor
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexNumAction(array $aParams)
    {
        (new Model)->updateNavigation($aParams[0]);

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oTemp = new \Engine\Response\Template();
        $oHelper->setUrl($oTemp->getUrl(
            $this->input->getRefererUrl()
        ));
        return $oHelper;
    }

}
