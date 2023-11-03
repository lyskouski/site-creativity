<?php namespace Modules\Info\Decor;

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

    /**
     * Get list of formating types
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        return $this->getSection($aParams, 'index');
    }

    /**
     * Get list of formating types for the forum
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function forumAction(array $aParams)
    {
        return $this->getSection($aParams, 'forum');
    }
}
