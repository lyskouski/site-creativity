<?php namespace Modules\Person\Work\Article;

/**
 * Controller to create new article
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\AbstractController
{
    /**
     * Has to be declared for other classes
     * @return Model
     */
    protected function getModel()
    {
        return (new Model);
    }

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action')
                    ->bindKey('num', array('ctype' => 'integer'))
                    ->bindKey('content')
                    ->bindKey('height')
                    ->bindKey('width')
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);

        if ($this->action === 'createAction') {
            (new \Access\Request\Topic)->updateAccess($oAccess, 'og:title');
            $oAccess->bindKey('og:image')
                ->bindKey('height')
                ->bindKey('width')
                ->bindKey('file')
                ->bindKey('category', array('list' => $this->getModel()->getCategories()), true);
        }

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Get current artwork entity
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     * @throws \Error\Validation
     */
    public function indexNumAction(array $aParams)
    {
        $list = $this->getModel()->getActual($aParams[0]);
        $this->response->titleOverride($list['og:title']. ' (' . \System\Registry::translation()->sys('LB_PERSON_DRAFT') . ')');

        // for a link highlighting
        $this->request->setParams(array('work'));
        $layout = new \Layouts\Helper\Editor($this->request, $this->response);

        $template = 'create';
        if ($list['list']) {
            if ($list['list'][0]->getAccess() === \Defines\User\Access::getAudit()) {
                $template = 'view';

            } elseif ($list['list'][0]->getAuthor() !== \System\Registry::user()->getEntity()) {
                throw new \Error\Validation('This draft is not yours');
            }
        }
        $layout->updateHeader($list);
        $layout->add("Entity/Article/{$template}", $list);
        /** @fixme {complete editor} 
        $layout->add("Editor/{$template}", $list);
        */
        return $layout;
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
        $oHelper->add('Entity/Article/new', array(
            'categories' => $model->getCategories(),
            'title_href' => $model->getUrl(),
            'img' => substr($model->getUrl(), strrpos($model->getUrl(), '/')+1)
        ));
        return $oHelper;
    }

    /**
     * Create new
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function createAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);

        $aPost = $this->input->getPost();

        $model = $this->getModel();
        $Url = $model->create($aPost, $model->getUrl(), $model->getTyped());
        $oHelper->setUrl(
            (new \Engine\Response\Template)->getUrl($Url),
            10,
            \System\Registry::translation()->sys('LB_ARTICLE_CREATED')
        );
        return $oHelper;
    }

    /**
     * Create new
     *
     * @param array $aParams
     * @return \Layouts\Helper\Zero
     */
    public function clearAction(array $aParams)
    {
        $this->getModel()->clearContent($aParams[0]);
        $oHelper = new \Layouts\Helper\Zero($this->request, $this->response);
        return $oHelper;
    }

    /**
     * Create new
     *
     * @param array $aParams
     * @return \Layouts\Helper\Zero
     */
    public function saveAction(array $aParams)
    {
        $this->getModel()->addContent(
            $aParams[0],
            $this->input->getPost('num'),
            $this->input->getPost('content')
        );
        $oHelper = new \Layouts\Helper\Zero($this->request, $this->response);
        return $oHelper;
    }

    /**
     * Change description
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function descriptionAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('work'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);

        $model = $this->getModel();
        $oHelper->add('Entity/Article/update', array(
            'title_href' => $model->getUrl(),
            'img' => substr($model->getUrl(), strrpos($model->getUrl(), '/')+1),
            'list' => $model->getDescriptions($aParams[0])
        ));
        return $oHelper;
    }

    public function updateAction(array $aParams)
    {
        $list = $this->input->getPost('content', array());
        $url = $this->getModel()->updateContent($list);

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl(
            (new \Engine\Response\Template)->getUrl($url)
        );
        return $oHelper;
    }

    /**
     * Create new
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function approveAction(array $aParams)
    {
        $this->getModel()->send4Review($aParams[0]);

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl(
            (new \Engine\Response\Template)->getUrl('person/work'),
            10,
            \System\Registry::translation()->sys('LB_TASK_WAIT_APPROVEMENT')
        );
        return $oHelper;
    }

}
