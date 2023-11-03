<?php namespace Modules\Dev\Tasks\Translation;

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
            ->addRequestMethod( \Defines\RequestMethod::POST )
                ->bindExtension( \Defines\Extension::JSON )
                    ->bindKey('token')
                    ->bindKey('getdata', array('list' => array('1')));

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
            'active' => 'dev/tasks#!/translation',
            'gui_status' => $oModel->getGuiStatus(),
            'text_status' => $oModel->getTextStatus(),
            'book_status' => $oModel->getBookStatus(),
        //    'task_status' => $oModel->getTaskStatus()
        ));
        return $oHelper;
    }

    /**
     * Return yandex token
     * @todo AES encrypt/decrypt option
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function yandexAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);
        $oHelper->add('Basic/null', array(
            'token' => \System\Registry::config()->getYandexKey()
        ));
        return $oHelper;
    }
}
