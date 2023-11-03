<?php namespace Modules\Dev;

/**
 * General controller for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $actions = ['edit', 'reply', 'modify', 'comment', 'create', 'translate', 'subtask'];
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
        // Search
        if (strpos($this->action, 'search') === 0) {
            $oAccess->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('ctype' => 'string'))
                    ->bindKey('/1', array('ctype' => 'integer', 'min' => -1))
                ->copyToExtension(\Defines\Extension::JSON);

        // Topics
        } elseif (isset($this->params[0]) && $this->params[0][0] === 'i') {
            // Validate topic parameters
            $oAccess->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('pattern' => '/^i\d{1,}$/'))
                    ->bindKey('/1', array('ctype' => 'integer', 'min' => -1))
                ->copyToExtension(\Defines\Extension::JSON);
            // Add comments
            $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => $actions))
                    ->bindKey('title', array('min_length' => 3, 'max_length' => 64))
                    ->bindKey('content', array('min_length' => 3))
                    // edit topic
                    ->bindKey('description', array('min_length' => 3, 'max_length' => 200))
                    ->bindKey('keywords', array('min_length' => 3, 'max_length' => 80))
                    ->bindKey('access', array('array_list' => array_keys(\Defines\User\Access::getList())))
                    ->bindKey('skip', array('list' => ['on', 1]))
                    // edit comment
                    ->bindKey('id', array('ctype' => 'integer'))
                    // translate topic
                    ->bindKey('language', array('list' => \Defines\Language::getList()));

            if (strpos($this->action, 'index') === 0) {
                $this->action = 'topicAction';
            }
        } elseif (strpos($this->action, 'modify') === 0) {
            $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => $actions))
                    ->bindKey('id', array('ctype' => 'integer'))
                    ->bindKey('content', array('min_length' => 3))
                    ->bindKey('access', array('array_list' => array_keys(\Defines\User\Access::getList())));
        // Validate new topic parameters
        } else {
            $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => $actions))
                    ->bindKey('title', array('min_length' => 3, 'max_length' => 64))
                    ->bindKey('description', array('min_length' => 3, 'max_length' => 200))
                    ->bindKey('content', array('min_length' => 3));
        }

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Main page for development section
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
        $oModel = new Model();
        $aCategory = array(
            'news',
            'contest',
            'bugs',
            'questions',
            'proposition',
            'offtopic'
        );
        foreach ($aCategory as $sName) {
            $aParams['list'][$sName] = $oModel->getList("dev/$sName", 0, 4);
        }

        $oHelper->add('index', $aParams);
        return $oHelper;
    }

    /**
     * Search by keys
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function searchAction(array $aParams)
    {
        $oTranslate = \System\Registry::translation();

        $iPage = $this->input->getGet('/1', 0);
        $sSearch = $this->input->getGet('/0', '', FILTER_SANITIZE_STRING);
        if ((string)(int)$sSearch === $sSearch) {
            $iPage = $sSearch;
            $sSearch = '';
        }

        $sTargetUrl = $this->request->getModuleUrl();

        $aData = (new Model)->getByKeys($sTargetUrl, $sSearch, $iPage);
        $aReqParams = array(
            'title' => \System\Registry::translation()->sys('LB_SITE_SEARCH') . ': ' . $sSearch,
            'title_href' => $sTargetUrl,
            'subtitle' => $oTranslate->get(['og:title', $sTargetUrl]),
            'subtitle_href' => $sTargetUrl,
            'languages' => null,
            'list' => $aData['list'],
            'count' => $aData['count'],
            'count_page' => $aData['count_page'],
            'count_url' => $this->input->getUrl(null),
            'page' => $iPage,
            'search' => $sSearch,
            'url' => $sTargetUrl,
            'module_url' => $this->request->getModuleUrl()
        );

        $oResult = new \Layouts\Helper\Dev($this->request, $this->response);
        $oResult->add('forum/forum_start', $aReqParams, __DIR__ . \Engine\Response\Template::VIEW_FOLDER);
        $oResult->add('forum/forum', $aReqParams, __DIR__ . \Engine\Response\Template::VIEW_FOLDER);
        return $oResult;
    }

    /**
     * Display topic
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function topicAction(array $aParams)
    {
        $oTranslate = \System\Registry::translation();
        $iPage = (int)$this->input->getGet('/1', 0);
        $sUrl = $this->input->getUrl(null);

        /* @var $oStat \Data\Doctrine\Main\ContentViews */
        $oStat = \System\Registry::stat();
        $aReqParams = array(
            'title' => $oTranslate->get(['og:title', $sUrl]),
            'title_href' => $sUrl,
            'list' => (new Model)->getTopicList($sUrl, $iPage),
            'stat' => $oStat,
            'page' => $iPage === -1 ? \Defines\Database\Params::getPageCount($oStat) : $iPage,
            'url' => $sUrl,
            'module_url' => $this->request->getModuleUrl() //substr($sUrl, 0, strpos($sUrl, $oInput->getGet('/0'))-1)
        );

        $oResult = new \Layouts\Helper\Dev($this->request, $this->response);
        $sPath = __DIR__ . \Engine\Response\Template::VIEW_FOLDER;

        $this->response->meta(new \Engine\Response\Meta\Script('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js'));
        $this->response->meta(new \Engine\Response\Meta\Script('//yastatic.net/share2/share.js'));
        $oResult->add('forum/topic_start', $aReqParams, $sPath);

        $oResult->add('forum/topic', $aReqParams, $sPath);
        return $oResult;
    }

    /**
     * Create new topic
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function createAction(array $aParams)
    {
        $sUrl = (new Model)->addTopic(
            $this->input->getUrl(null),
            $this->input->getPost('title'),
            $this->input->getPost('description'),
            $this->input->getPost('content')
        );

        $oResult = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oResult->setUrl((new \Engine\Response\Template)->getUrl($sUrl));
        return $oResult;
    }

    /**
     * Edit topic description
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function editAction(array $aParams)
    {
        if ($this->input->getPost('title')) {
            $sUrl = (new Model)->editTopic(
                $this->input->getUrl(null),
                $this->input->getPost('title'),
                $this->input->getPost('description'),
                $this->input->getPost('keywords'),
                $this->input->getPost('access'),
                $this->input->getPost('skip', false) ? true : false
            );
            $oResult = new \Layouts\Helper\Redirect($this->request, $this->response);
            $oResult->setUrl((new \Engine\Response\Template)->getUrl($sUrl));
        } else {
            $oResult = new \Layouts\Helper\Dev($this->request, $this->response);
            $sPath = __DIR__ . \Engine\Response\Template::VIEW_FOLDER;
            $oResult->add('forum/topic_edit', $aParams, $sPath);
        }
        return $oResult;
    }

    /**
     * Add comment in topic
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function replyAction(array $aParams)
    {
        (new Model)->addComment($this->input->getUrl(null), $this->input->getPost('content'));
        return $this->topicAction($aParams);
    }

    /**
     * Edit comment in topic
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function modifyAction(array $aParams)
    {
        \System\Registry::translation()->skipUpdate();
        $sPath = __DIR__ . \Engine\Response\Template::VIEW_FOLDER;
        $aParams['comment'] = (new \Data\ContentHelper)->getEntityManager()->find(
            \Defines\Database\CrMain::CONTENT,
            $this->input->getPost('id')
        );
        if ($this->input->getPost('content')) {
            $sUrl = (new Model)->editComment(
                $aParams['comment'],
                $this->input->getPost('content'),
                $this->input->getPost('access')
            );
            $oResult = new \Layouts\Helper\Redirect($this->request, $this->response);
            $oResult->setUrl((new \Engine\Response\Template)->getUrl($sUrl));
        } else {
            $oResult = new \Layouts\Helper\Dev($this->request, $this->response);
            $oResult->add('forum/comment_edit', $aParams, $sPath);
        }
        return $oResult;
    }

    /**
     * Init translation machanism for the topic
     * @param array $aParams
     */
    public function translateAction(array $aParams)
    {
        $sLanguage = $this->input->getPost('language');
        $sUrl = $this->input->getUrl(null);
        (new Model)->createTransaltion($sUrl, $sLanguage);

        $oResult = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oResult->setUrl((new \Engine\Response\Template)->getUrl('/dev/tasks/translation/text', null, $sLanguage));
        return $oResult;
    }

    /**
     * Common behaviour for each forum section
     *
     * @param array $aParams
     * @param string $sType
     * @return \Layouts\Helper\Basic
     * @throws \Error\Validation
     */
    protected function getSection($aParams, $sType, $iCount = 0)
    {
        $iPage = $this->input->getGet('/0', 0);
        $sTargetUrl = "dev/{$sType}";

        $aReqParams = array(
            'title' => \System\Registry::translation()->sys('LB_SITE_SUPPORT'),
            'title_href' => 'dev',
            'subtitle' => \System\Registry::translation()->sys('LB_FORUM_' . strtoupper($sType)),
            'subtitle_href' => $sTargetUrl,
            'list' => (new Model)->getList($sTargetUrl, $iPage),
            'stat' => (new \Data\ContentHelper)->getRepository()->getPages($sTargetUrl),
            'page' => $iPage,
            'url' => $sTargetUrl,
            'module_url' => $this->request->getModuleUrl()
        );

        $oResult = new \Layouts\Helper\Dev($this->request, $this->response);
        $oResult->add('forum/forum_start', $aReqParams, __DIR__ . \Engine\Response\Template::VIEW_FOLDER);
        $oResult->add('forum/forum', $aReqParams, __DIR__ . \Engine\Response\Template::VIEW_FOLDER);
        return $oResult;
    }

}
