<?php namespace Modules\Mind\Trainer;

/**
 * Mind Trainer Controller
 *
 * @since 2016-12-26
 * @author Viachaslau Lyskouski
 */
abstract class AbstractController extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
                ->bindNullKey()
            ->copyToExtension(\Defines\Extension::JSON)
        ->addRequestMethod(\Defines\RequestMethod::POST)
            ->bindExtension(\Defines\Extension::JSON)
                ->bindKey('action', array('ctype' => 'string'))
                ->bindKey('content', array('ctype' => 'string'));
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    abstract function getModel();

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        $aParams['rating'] = $this->getModel()->getGameRating();
        $this->request->setParams($aParams);
        $oHelper = new \Layouts\Entity\Mind($this->request, $this->response);
        $oHelper->add('index', [], $this->getTmplPath());
        return $oHelper;
    }

    public function startAction(array $aParams)
    {
        $oHelper = new \Layouts\Entity\Mind($this->request, $this->response);
        $oHelper->add('game', $this->getModel()->getGameAttr(), $this->getTmplPath());
        return $oHelper;
    }

    public function progressAction(array $aParams)
    {
        $this->getModel()->updateGame($this->input->getPost('content'));
        return new \Layouts\Helper\Zero($this->request, $this->response);
    }

    public function stopAction(array $aParams)
    {
        $summary = $this->input->getPost('content');
        if ($summary) {
            $this->getModel()->updateGame(explode('|', $summary), true);
        }
        $oHelper = new \Layouts\Entity\Mind($this->request, $this->response);
        $oHelper->add('stat', $this->getModel()->finalizeGame(), $this->getTmplPath());
        return $oHelper;
    }

}
