<?php namespace Modules\Dev\Board;

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
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('sanitize' => FILTER_VALIDATE_INT))
                ->copyToExtension(\Defines\Extension::JSON)
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => ['move', 'subtask']))
                    ->bindKey('pattern', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('type', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('subtask');

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
        $model = new Model();
        // for a link highlighting
        $this->request->setParams(array('board'));
        $response = new \Layouts\Helper\Dev($this->request, $this->response);

        $response->add('index', array(
            'new' => $model->getNewTasks(),
            'subtask' => $model->getSubtaskList(),
            'list_new' => $model->getTasks(\Defines\Database\BoardCategory::RECENT),
            'list_active' => $model->getTasks(\Defines\Database\BoardCategory::ACTIVE),
            'list_finish' => $model->getTasks(\Defines\Database\BoardCategory::FINISH, 5),
            'active' => 'dev/board'
        ));
        return $response;
    }

    public function moveAction(array $aParams)
    {
        (new Model)->changeStatus($this->input->getPost('pattern'), $this->input->getPost('type'));
        return $this->indexAction($aParams);
    }



    public function subtaskAction(array $aParams)
    {
        (new Model)->changeSubtask($this->input->getPost('subtask'));
        return $this->indexAction($aParams);
    }

    public function recentAction(array $aParams)
    {
        $numPage = $this->input->getGet('/0', 0);
        $response = new \Layouts\Helper\Dev($this->request, $this->response);
        return $response->add('overview',
            (new Model)->getPageTasks(
                \Defines\Database\BoardCategory::RECENT,
                $numPage,
                \System\Registry::translation()->sys('LB_TODO_DO'),
                'dev/board/recent'
            )
        );
    }

    public function activeAction(array $aParams)
    {
        $numPage = $this->input->getGet('/0', 0);
        $response = new \Layouts\Helper\Dev($this->request, $this->response);
        return $response->add('overview',
            (new Model)->getPageTasks(
                \Defines\Database\BoardCategory::ACTIVE,
                $numPage,
                \System\Registry::translation()->sys('LB_TODO_IN'),
                'dev/board/active'
            )
        );
    }

    public function finishAction(array $aParams)
    {
        $numPage = $this->input->getGet('/0', 0);
        $response = new \Layouts\Helper\Dev($this->request, $this->response);
        return $response->add('overview',
            (new Model)->getPageTasks(
                \Defines\Database\BoardCategory::FINISH,
                $numPage,
                \System\Registry::translation()->sys('LB_TODO_OK'),
                'dev/board/finish'
            )
        );
    }

    public function rejectAction(array $aParams)
    {
        $numPage = $this->input->getGet('/0', 0);
        $response = new \Layouts\Helper\Dev($this->request, $this->response);
        return $response->add('overview',
            (new Model)->getPageTasks(
                \Defines\Database\BoardCategory::DELETE,
                $numPage,
                \System\Registry::translation()->sys('LB_TODO_NO'),
                'dev/board/reject'
            )
        );
    }
}
