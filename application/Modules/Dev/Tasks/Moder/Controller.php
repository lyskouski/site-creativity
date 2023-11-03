<?php namespace Modules\Dev\Tasks\Moder;

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
                    ->bindNullKey()
                ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindNullKey();

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * @todo
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        $oModel = new Model();
        // for a link highlighting
        $this->request->setParams(array('tasks'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);

        $oHelper->add('index', array(
            'menu' => $oModel->getNavigation(),
            'active' => 'dev/tasks#!/moder',
            'topics' => $oModel->getTopics(),
            'comments' => $oModel->getComments(),
            'reply' => $oModel->getReply(),
            'quote' => $oModel->getQuote()
        ));
        return $oHelper;
    }

}
