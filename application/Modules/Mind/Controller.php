<?php namespace Modules\Mind;

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

        $oRepository = (new \Data\ContentHelper)->getRepository();
        $aData = $oRepository->findLastTopics('mind/%', 0, 5);
        $oHelper->add('index', array(
            'list' => array_values($aData[\Defines\Content\Attribute::TYPE_TITLE])
        ));
        return $oHelper;
    }

}
