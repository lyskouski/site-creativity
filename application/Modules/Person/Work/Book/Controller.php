<?php namespace Modules\Person\Work\Book;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\Person\Work\Poetry\Controller
{

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
                    ->bindKey('isbn')
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);

        if ($this->action === 'createAction') {
            $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => ['create', 'comment']))
                    ->bindKey('content#0')
                    ->bindKey('og:title', array('min_length' => 3, 'max_length' => 255), true)
                    ->bindKey('description', array('min_length' => 3, 'max_length' => 255), true)
                    ->bindKey('og:image')
                    ->bindKey('keywords', array('sanitize' => FILTER_SANITIZE_STRING), true)
                    ->bindKey('isbn', null, true)
                    ->bindKey('udc', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('author', array('sanitize' => FILTER_SANITIZE_STRING), true)
                    ->bindKey('date', array('sanitize' => FILTER_SANITIZE_STRING), true)
                    ->bindKey('pageCount', array('ctype' => 'integer', 'min' => 1), true)
                    ->bindKey('height')
                    ->bindKey('width')
                    ->bindKey('file');
            $this->input->setPost(
                'isbn',
                (new \Access\Request\Params)->getIsbn()
            );
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
        $oHelper->add('Entity/Book/isbn', array(
            'title_href' => $model->getUrl(),
            'img' => substr($model->getUrl(), strrpos($model->getUrl(), '/')+1)
        ));
        return $oHelper;
    }

        /**
     * Show initial create panel
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     */
    public function findAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('work'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);

        $model = $this->getModel();
        $path = $model->getUrl();
        $params = array(
            'title_href' => $path,
            'og:image' => (new \System\Minify\Images)->adaptWorkUrl($path),
            'isbn' => (new \Access\Request\Params)->getIsbn()
        );
        return $oHelper->add(
            'Entity/Book/new',
            $model->findNewBook($params['isbn'], $params)
        );
    }

}
