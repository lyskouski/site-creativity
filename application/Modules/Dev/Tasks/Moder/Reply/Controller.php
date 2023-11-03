<?php namespace Modules\Dev\Tasks\Moder\Reply;

/**
 * Operate with topic comments
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Dev/Tasks/Moder/Comments
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
                    ->bindKey('/0', array('list' => \Defines\Language::getList()))
                ->copyToExtension(\Defines\Extension::JSON)
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('id', array('ctype' => 'integer'))
                    ->bindKey('content', array('min_length' => 3))
                    ->bindKey('action', array('list' => ['approve', 'decline', 'edit', 'delete']));

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    protected function initModel()
    {
        $this->model = new Model;
    }

    /**
     * @todo
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        if (!$aParams) {
            $aParams[0] = \System\Registry::translation()->getTargetLanguage();
        }

        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
        return $oHelper->add('index', array(
            'language' => $aParams[0],
            'url' => 'dev/tasks/moder/reply',
            'list' => $this->model->getList($aParams[0])
        ));
    }

    public function approveAction(array $aParams)
    {
        $this->model->changeStatus(
            $this->input->getPost('id'),
            \Defines\User\Access::getModApprove()
        );
        return $this->indexAction($aParams);
    }

    public function declineAction(array $aParams)
    {
        $this->model->changeStatus(
            $this->input->getPost('id'),
            \Defines\User\Access::getModDecline()
        );
        return $this->indexAction($aParams);
    }

    public function deleteAction(array $aParams)
    {
        $id = $this->input->getPost('id');
        $em = \System\Registry::connection();
        $entity = $em->find(\Defines\Database\CrMain::CONTENT, $id);
        if ($entity && strpos($entity->getType(), 'comment#') === 0) {
            $em->remove($entity);
            $em->flush();
        } else {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_403'),
                \Defines\Response\Code::E_FORBIDDEN
            );
        }
        return $this->indexAction($aParams);
    }

    public function editAction(array $aParams)
    {
        $id = $this->input->getPost('id');
        $new = $this->input->getPost('content');
        $oData = $this->model->updateComment($id, $new);
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
        if ($new) {
            $oResult = $this->indexAction($aParams);
        } else {
            $oResult = $oHelper->add('edit', array(
                'id' => $id,
                'url' => $this->input->getUrl(null),
                'content' => $oData
            ));
        }
        return $oResult;
    }
}
