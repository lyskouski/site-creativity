<?php namespace Modules\Dev\Tasks\Translation\Text;

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
    /**
     * @var Model
     */
    protected $model;

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('type' => 'integer'))
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', ['sanitize' => FILTER_SANITIZE_STRING])
                    ->bindKey('id', ['ctype' => 'integer'])
                    ->bindKey('list')
                    ->bindKey('next')
                    ->bindKey('height')
                    ->bindKey('width');

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function initModel()
    {
        $this->model = new Model();
    }

    /**
     * Page with a confirmation request
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexAction(array $aParams)
    {
        $iHasTaks = $this->model->checkTask();
        if ($iHasTaks) {
            $oHelper = $this->indexNumAction(array($iHasTaks));

        } else {
            // for a link highlighting
            $this->request->setParams(array('tasks'));
            $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
            $sActive = current($aParams) ? current($aParams) : \Defines\Language::getDefault();

            $oHelper->add('index', array(
                'url_active' => $sActive,
                'is_post' => $this->request->getRequestMethod() === \Defines\RequestMethod::POST,
                'list' => $this->model->getTaskList()
            ), $this->getTmplPath());
        }
        return $oHelper;
    }

    /**
     * Show current task
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexNumAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('tasks'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
        $oHelper->add('task', $this->model->getTask($aParams[0]), $this->getTmplPath());
        return $oHelper;

    }

    /**
     * Create a random task for a user
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function taskAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oTemp = new \Engine\Response\Template();
        $oHelper->setUrl($oTemp->getUrl(
            $this->request->getModuleUrl() . '/' . $this->model->getNewTask($this->input->getPost('id'))
        ));
        return $oHelper;
    }

    /**
     * Send changes for an approvement
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function saveAction(array $aParams)
    {
        $this->model->saveTask($this->input->getPost('list'));

        if ($this->input->getPost('next')) {
            $oHelper = $this->taskAction($aParams);
        } else {
            $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
            $oTemp = new \Engine\Response\Template();
            $oHelper->setUrl(
                $oTemp->getUrl( $this->request->getModuleUrl() ),
                15,
                \System\Registry::translation()->sys('LB_TASK_WAIT_APPROVEMENT') . '. '
            );
        }
        return $oHelper;
    }

}
