<?php namespace Modules\Dev\Tasks\Moder\Comments;

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
                    ->bindKey('action', array('list' => ['approve', 'decline', 'edit']));

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
        if (!$aParams) {
            $aParams[0] = \System\Registry::translation()->getTargetLanguage();
        }

        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
        return $oHelper->add('index', array(
            'language' => $aParams[0],
            'list' => (new Model)->getList($aParams[0])
        ));
    }

    public function approveAction(array $aParams)
    {
        (new Model)->changeStatus(
            $this->input->getPost('id'),
            \Defines\User\Access::getModApprove()
        );
        return $this->indexAction($aParams);
    }

    public function declineAction(array $aParams)
    {
        (new Model)->changeStatus(
            $this->input->getPost('id'),
            \Defines\User\Access::getModDecline(),
            true
        );
        return $this->indexAction($aParams);
    }

    public function editAction(array $aParams)
    {
        $oContent = (new Model)->getTopic($aParams[0]);
        $this->input->setServer('REQUEST_URI', "/{$oContent->getLanguage()}/{$oContent->getPattern()}." . \Defines\Extension::JSON);
        \System\Registry::updateStatistics();
        return $this->forward('Dev', 'modify', $aParams);
    }
}
