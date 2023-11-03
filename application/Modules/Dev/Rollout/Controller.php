<?php namespace Modules\Dev\Rollout;

/**
 * Release management controller
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\AbstractController
{

    /**
     * @var integer - seconds before reload
     */
    protected $timeout = 10;

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
                ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => ['check', 'alpha', 'test', 'beta', 'clear', 'release', 'live']))
                    ->bindKey('revision', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('version', array('sanitize' => FILTER_SANITIZE_NUMBER_INT))
                    ->bindKey('title', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('live', array('pattern' => '/^[0-9\.]{1,}$/'));

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
        // for a link highlighting
        $this->request->setParams(array('rollout'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);

        $model = new Model();
        $oHelper->add('index', array(
            'alpha' => $model->getVersionAlpha(),
            'beta' => $model->getVersionBeta(),
            'live' => $model->getVersionLive(),
            'last' => $model->getReleasedLive(),
            'version' => $model->getVersion(),
            'hg' => $model->getRelease(),
            'live_list' => $model->getLiveList(),
            'local' => $model->getChanges(),
            'phpunit' => $model->getTestsResult()
        ));
        return $oHelper;
    }

    public function checkAction(array $aParams)
    {
        (new Model)->getMercurial()->setPull();

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl((new \Engine\Response\Template)->getUrl('dev/rollout'), $this->timeout);
        return $oHelper;
    }

    public function clearAction(array $aParams)
    {
        (new Model)->runPhing('truncate');
        /* @var $oHelper \Layouts\Helper\Redirect */
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl((new \Engine\Response\Template)->getUrl('dev/rollout'), $this->timeout);
        return $oHelper;
    }

    public function alphaAction(array $aParams)
    {
        $revision = $this->input->getPost('revision');
        if (!$revision) {
            throw new \Error\Validation('Incorrect revision');
        }

        $result = (new Model)->getMercurial()->setUpdate((int) $revision);

        /* @var $oHelper \Layouts\Helper\Redirect */
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl((new \Engine\Response\Template)->getUrl('dev/rollout'), $this->timeout, nl2br($result));
        return $oHelper;
    }

    public function testAction(array $aParams)
    {
        $result = (new Model)->runPhing('test');
        /* @var $oHelper \Layouts\Helper\Redirect */
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl((new \Engine\Response\Template)->getUrl('dev/rollout'), $this->timeout, nl2br($result));
        return $oHelper;
    }

    public function betaAction(array $aParams)
    {
        $result = (new Model)->runPhing();
        /* @var $oHelper \Layouts\Helper\Redirect */
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl((new \Engine\Response\Template)->getUrl('dev/rollout'), $this->timeout, nl2br($result));
        return $oHelper;
    }

    public function releaseAction(array $aParams)
    {
        $version = $this->input->getPost('live');
        $title = $this->input->getPost('title');
        (new Model)->createLiveRelease($version, $title);

        return $this->indexAction($aParams);
    }

    public function liveAction(array $aParams)
    {
        $id = $this->input->getPost('live');
        (new Model)->setLive($id);

        return $this->indexAction($aParams);
    }
}
