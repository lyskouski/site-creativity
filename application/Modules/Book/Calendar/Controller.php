<?php namespace Modules\Book\Calendar;

/**
 * Book overview controller
 * @see \Modules\AbstractController
 *
 * @since 2016-03-30
 * @author Viachaslau Lyskouski
 * @package Modules/Book/Overview
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        // To avoid autocreate content
        \System\Registry::translation()->skipUpdate();

        $access = new Permission($this->action, $this->input->getPost('isbn'));
        return $access->validate(
            $this->request->getRequestMethod(),
            $this->request->getResponseType()
        );
    }

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        $model = new Model();
        $entity = $model->getEntity($aParams);
        if ($entity) {
            $this->request->setParams(array('calendar'));
            $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
            $oHelper->add('calendar', array(
                'href' => $entity->getPattern(),
                'entity' => $entity,
                'pages/day' => $model->getReadingSpeed(),
                'list' => $model->getList($entity->getId()),
                'statistics' => $model->getStatistics($entity)
            ));
        } else {
            $oHelper = $this->newAction($aParams);
        }
        return $oHelper;
    }

    public function editAction(array $aParams)
    {
        $model = new Model();
        $entity = $model->getEntity($aParams);

        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
        $oHelper->add('Entity/Article/update', array(
            'title_href' => $entity->getPattern(),
            'img' => \System\Registry::translation()->get(['og:image', $entity->getPattern()]),
            'list' => $model->prepareContentList($entity)
        ));
        return $oHelper;
    }

    public function updateAction(array $aParams)
    {
        $model = new Model();
        $entity = $model->getEntity($aParams);
        $model->updateContent($entity, $this->input->getPost('content'));
        return $this->indexAction($aParams);
    }

    public function newAction(array $aParams)
    {
        $this->request->setParams(array('calendar'));
        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
        return $oHelper->add('new', array(
            'list' => (new Model)->getAllList(false)
        ));
    }

    public function createAction(array $aParams)
    {
        $url = (new Model)->createList($this->input->getPost('title'));

        $oResult = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oResult->setUrl((new \Engine\Response\Template)->getUrl($url));
        return $oResult;
    }

    public function searchAction(array $aParams)
    {
        $model = new Model();
        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
        if (!$this->input->getPost('type')) {
            $oHelper->add('search_option', array(
                'list' => $model->getSourceList($this->input->getPost('language'))
            ));
            return $oHelper;
        }

        $bookList = $model->findBooks(
            (new \Access\Request\Params)->getIsbn(null, false),
            $this->input->getPost('author'),
            $this->input->getPost('title'),
            $this->input->getPost('language'),
            $this->input->getPost('type')
        );

        $list = array();
        /* @var $book \Engine\Book\Result\Book */
        foreach ($bookList as $book) {
            if (!$book->getIsbn()) {
                continue;
            }
            $a = array(
                'callback' => 'cr_move ui" data-class="Modules/Book/Calendar" data-actions="move" data-type="' 
                    . $this->input->getPost('type') . '" data-isbn="' . $book->getIsbn(),
                'draggable' => true,
                'author_txt' => $book->getAuthor(),
                'title' => $book->getTitle(),
                'text' => $book->getDescription(),
                'img' => $book->getImage(),
                'img_type' => $book->getImageType(),
                'updated_at' => $book->getDate()
            );
            $list[] = $a;
            if (sizeof($list) === 8) {
                break;
            }
        }

        $oHelper->add('search', array(
            'list' => $list
        ));
        return $oHelper;
    }

    public function moveAction(array $aParams)
    {
        $model = new Model();
        $entity = $model->getEntity($aParams);
        $idList = $entity->getId();

        $model->move2List($idList, $this->input->getPost());

        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
        $oHelper->add('calendar/search', array(
            'list' => $model->getList($idList),
            'pages/day' => $model->getReadingSpeed(),
            'entity' => $entity
        ));
        return $oHelper;
    }

    public function pageAction(array $aParams)
    {
        $model = new Model();
        $entity = $model->getEntity($aParams);
        $idList = $entity->getId();

        $model->setPage($idList, (new \Access\Request\Params)->getIsbn(), $this->input->getPost('page'));

        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
        $oHelper->add('calendar/stat', array(
            'list' => $model->getList($idList),
            'entity' => $entity,
            'search' => true
        ));
        return $oHelper;
    }

    public function trashAction(array $aParams)
    {
        $oTranslate = \System\Registry::translation();

        $model = new Model();
        $entity = $model->getEntity($aParams);

        if (!$entity) {
            throw new \Error\Validation($oTranslate->sys('LB_HEADER_404'), \Defines\Response\Code::E_BAD_REQUEST);
        }
        $this->response->titleOverride($oTranslate->sys('LB_BOOK_LIST_DELETE') . " ({$entity->getContent()})");
        \System\Registry::translation()->skipUpdate();

        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
        $oHelper->add('trash', array(
            'list' => $model->getList($entity->getId(), \Defines\Database\BookCategory::DELETE),
            'entity' => $entity
        ));
        return $oHelper;
    }

    public function restoreAction(array $aParams)
    {
        $model = new Model();
        $id = $this->input->getPost('id');
        $entity = $model->restoreBookRead($id);

        $count = $model->getList($entity->getContent()->getId(), \Defines\Database\BookCategory::DELETE, true);
        if ($count) {
            $layout = $this->trashAction($aParams);

        } else {
            $layout = new \Layouts\Helper\Redirect($this->request, $this->response);
            $layout->setUrl((new \Engine\Response\Template)->getUrl($entity->getContent()->getPattern()));
        }

        return $layout;
    }

    public function removeAction(array $aParams)
    {
        $id = $this->input->getPost('id');
        (new Model)->removeBookRead($id);

        if (strpos($this->input->getUrl(null), '/trash') !== false) {
            $action = $this->trashAction($aParams);
        } else {
            $action = $this->indexAction($aParams);
        }
        return $action;
    }

    public function changeAction(array $aParams)
    {
        $model = new Model();
        $id = $this->input->getPost('id');
        $entity = $model->getEntity($aParams);

        if (!$entity) {
            throw new \Error\Validation(
                \Defines\Response\Code::getHeader(\Defines\Response\Code::E_BAD_REQUEST, null),
                \Defines\Response\Code::E_BAD_REQUEST
            );
        }

        if ($this->input->getPost('list')) {
            $model->removeBookRead($id);

            $listId = 'i' . $this->input->getPost('list');
            $model->getEntity([$listId]);

            $model->move2List($this->input->getPost('list'), ['isbn' => $id, 'type' => \Defines\Database\BookCategory::READ , 'pos' => 0]);
            $layout = $this->indexAction([$listId]);

        } else {
            $layout = new \Layouts\Helper\Book($this->request, $this->response);
            $layout->add('change', array(
                'list' => $model->getAllList(false),
                'entity' => $entity,
                'book' => \System\Registry::connection()->find(\Defines\Database\CrMain::BOOK, $id)
            ));
        }

        return $layout;
    }
}
