<?php namespace Modules\Mind\Trainer;

/**
 * Mind Trainer Controller
 *
 * @since 2016-12-26
 * @author Viachaslau Lyskouski
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
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('mind'));
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);

        $oHelper->add('index');
        return $oHelper;
    }

}
