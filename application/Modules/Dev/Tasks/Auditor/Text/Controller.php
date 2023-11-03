<?php namespace Modules\Dev\Tasks\Auditor\Text;

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
                    ->bindKey('action')
                    ->bindKey('reply')
                    ->bindKey('username')
                    ->bindKey('next');

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Show summary information
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexAction(array $aParams)
    {
        $iHasTaks = (new Model)->checkTask();
        if ($iHasTaks) {
            return $this->indexNumAction(array($iHasTaks));
        }

        // for a link highlighting
        $this->request->setParams(array('tasks'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
        $oHelper->add('task', array(
            'is_post' => $this->request->getRequestMethod() === \Defines\RequestMethod::POST
        ));
        return $oHelper;
    }

    /**
     * Task for an auditor
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexNumAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('tasks'));

        $aTask = (new Model)->getTask();
        if ($aTask) {
            $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
            $oHelper->add('approve', array(
                'list' => $aTask,
                'next' => (boolean) strpos($this->input->getRefererUrl(), '/text/')
            ));
        } else {
            $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
            $oTemp = new \Engine\Response\Template();
            $oHelper->setUrl($oTemp->getUrl($this->request->getModuleUrl()));
        }

        return $oHelper;
    }

    /**
     * Assign task to the current auditor
     *
     * @return \Layouts\Helper\Redirect
     */
    public function getAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oTemp = new \Engine\Response\Template();
        $oHelper->setUrl($oTemp->getUrl($this->request->getModuleUrl() . '/' . (new Model)->getNewTask()));
        return $oHelper;
    }

    public function approveAction(array $aParams)
    {
        (new Model)->approveTask();

        if ($this->input->getPost('next')) {
            $oHelper = $this->getAction($aParams);
        } else {
            $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
            $oTemp = new \Engine\Response\Template();
            $oHelper->setUrl(
                $oTemp->getUrl($this->request->getModuleUrl()),
                15,
                \System\Registry::translation()->sys('LB_CONTENT_WAS_PUBLISHED') . '<br />'
            );
        }
        return $oHelper;
    }

    /**
     * Change author
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function authorAction(array $aParams)
    {
        $model = new Model();
        $model->changeAuthor($this->input->getPost('username'));
        return $this->indexNumAction([$model->checkTask()]);
    }

    public function rejectAction(array $aParams)
    {
        (new Model)->rejectTask(
            $this->input->getPost('reply', '', FILTER_SANITIZE_STRING)
        );
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oTemp = new \Engine\Response\Template();
        $oHelper->setUrl(
            $oTemp->getUrl( $this->request->getModuleUrl() ),
            15,
            \System\Registry::translation()->sys( 'LB_CONTENT_WAS_REJECTED' ) . '<br />'
        );
        return $oHelper;
    }

    public function deleteAction(array $aParams)
    {
        (new Model)->deleteTask();
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);

        $oHelper->setUrl(
            (new \Engine\Response\Template)->getUrl( $this->request->getModuleUrl() )
        );
        return $oHelper;
    }

    /**
     * Action for the main editor
     * @note possibility to edit text
     *
     * @param array $aParams
     */
    public function editAction (array $aParams) {

    }

}
