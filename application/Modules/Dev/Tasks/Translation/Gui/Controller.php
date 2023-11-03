<?php namespace Modules\Dev\Tasks\Translation\Gui;

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

    protected $aList;

    public function __construct(\Engine\Request\Params $oParams, $bStopInit = false)
    {
        $this->aList = (new Model)->getList();
        parent::__construct($oParams, $bStopInit);
    }

    protected function initAllowed()
    {
        $aFilterLang = array('list' => \Defines\Language::getList());
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', $aFilterLang)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('/0', $aFilterLang)
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action')
                    ->bindKey('language', $aFilterLang)
                    ->bindKey('list', array('keys' => $this->aList));

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function indexAction(array $aParams)
    {
        $this->response->meta(new \Engine\Response\Meta\Script('lib/aes'));
        // for a link highlighting
        $this->request->setParams(array('tasks'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);

        $sActive = current($aParams) ? current($aParams) : \Defines\Language::getDefault();
        $oHelper->add('index', array(
            'url_active' => $sActive,
            'list' => $this->aList,
            'is_post' => $this->request->getRequestMethod() === \Defines\RequestMethod::POST
        ));
        return $oHelper;
    }

    public function saveAction(array $aParams)
    {
        (new Model)->saveList(
            $this->input->getPost('language'),
            $this->input->getPost('list', [])
        );
        $this->aList = (new Model)->getList();
        return $this->indexAction($aParams);
    }
}
