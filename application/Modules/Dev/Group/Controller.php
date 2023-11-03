<?php namespace Modules\Dev\Group;

/**
 * General controller for index page
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
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => ['view', 'delete', 'add']))
                    ->bindKey('group', array('ctype' => 'integer'))
                    ->bindKey('user_access', array('ctype' => 'integer'))
                    ->bindKey('username', array('filter' => FILTER_SANITIZE_STRING));

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Get initial page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('group'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);

        $model = new Model();
        $list = $model->getUserTypes();
        if (array_key_exists('current', $aParams)) {
            $curr = $aParams['current'];
        } else {
            $curr = current($list);
        }
        $oHelper->add('index', array(
            'roles' => $list,
            'counts' => $model->getGroupsCount(),
            'active' => $curr->getId(),
            'current' => $curr,
            'locked' => $model->getLocked(),
            'users' => $model->getUsersByRole($curr)
        ));
        return $oHelper;
    }

    /**
     * Get user list
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function viewAction(array $aParams)
    {
        $aParams['current'] = (new Model)->getAccessById($this->input->getPost('group'));
        return $this->indexAction($aParams);
    }

    /**
     * Get user list
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function deleteAction(array $aParams)
    {
        (new Model)->removePrivilegue($this->input->getPost('user_access'));
        return $this->viewAction($aParams);
    }

    /**
     * Get user list
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function addAction(array $aParams)
    {
        (new Model)->addPrivilegue($this->input->getPost('username'), $this->input->getPost('group'));
        return $this->viewAction($aParams);
    }
}
