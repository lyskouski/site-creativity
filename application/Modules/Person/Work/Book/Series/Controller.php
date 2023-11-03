<?php namespace Modules\Person\Work\Book\Series;

/**
 * Controller for book series creation
 * @see \Modules\AbstractController
 *
 * @since 2016-08-25
 * @author Viachaslau Lyskouski
 * @package Modules/Person/Work/Book/Series
 */
class Controller extends \Modules\Person\Work\Poetry\Controller
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action')
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);

        switch ($this->action) {
            case 'createAction':
            case 'changeAction':
            case 'updateAction':
                $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                    ->bindExtension(\Defines\Extension::JSON)
                    //    ->bindKey('action', array('list' => ['create']))
                        ->bindKey('og:title', array('min_length' => 3, 'max_length' => 255), true)
                        ->bindKey('description', array('min_length' => 3, 'max_length' => 255), true)
                        ->bindKey('og:image')
                        ->bindKey('keywords', array('sanitize' => FILTER_SANITIZE_STRING), true)
                        ->bindKey('height')
                        ->bindKey('width')
                        ->bindKey('file')
                        ->bindKey('content#0');
                break;
            case 'patternAction':
                $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                    ->bindExtension(\Defines\Extension::JSON)
                        ->bindKey('pattern');
                break;
            case 'saveAction':
                $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                    ->bindExtension(\Defines\Extension::JSON)
                        ->bindKey('content')
                        ->bindKey('num', array('list' => ['0']));
                break;
        }

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Show initial create panel
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     */
    public function indexAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('work'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);

        $model = $this->getModel();
        $oHelper->add('index', array(
            'title_href' => $model->getUrl(),
            'og:image' => '/img/css/el_notion/work/book/series_type.svg',
            'list' => (new \Modules\Book\Calendar\Model)->getAllList(false)
        ));
        return $oHelper;
    }

    public function changeAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('work'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);

        $model = $this->getModel();
        $params = $this->input->getPost();
        $params['title_href'] = $model->getUrl() . '/' . $aParams[0];
        $params['action'] = 'update';
        $oHelper->add('index', $params);
        return $oHelper;
    }

    public function updateAction(array $aParams)
    {
        $model = $this->getModel();
        $list = array();
        // request
        $postList = $this->input->getPost();
        // db state
        $dbList = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW)
            ->findByPattern($model->getUrl() . '/' . $aParams[0]);

        foreach ($dbList as $e) {
            $list[$e->getId()] = $postList[$e->getType()];
        }
        $url = $this->getModel()->updateContent($list);

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl(
            (new \Engine\Response\Template)->getUrl($url)
        );
        return $oHelper;
    }

    /**
     * Show initial create panel
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     */
    public function patternAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('work'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);

        $pattern = $this->input->getPost('pattern');
        $oTranslate = \System\Registry::translation();

        $model = $this->getModel();
        $oHelper->add('index', array(
            'title_href' => $model->getUrl(),
            'og:image' => $oTranslate->get(['og:image', $pattern]),
            'og:title' => $oTranslate->get(['og:title', $pattern]),
            'keywords' => $oTranslate->get(['keywords', $pattern]),
            'description' => $oTranslate->get(['description', $pattern]),
            'content#0' => implode(',', $model->getBookList($pattern)),
            'list' => (new \Modules\Book\Calendar\Model)->getAllList(false)
        ));
        return $oHelper;
    }

    public function indexNumAction(array $aParams)
    {
        $oHelper = parent::indexNumAction($aParams);
        $oTemplate = $oHelper->get($this->pos);
        if (strpos($oTemplate->getTemplate(), 'Poetry') || strpos($oTemplate->getTemplate(), 'Editor') === 0) {
            $oTemplate->changeTemplate("Entity/Book/Series/create");
        }
        return $oHelper;
    }

    public function saveAction(array $aParams)
    {
        $this->pos = 0;
        parent::saveAction($aParams);
        return $this->indexNumAction($aParams);
    }
}
