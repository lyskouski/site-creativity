<?php namespace Modules\Book\Overview;

use Defines\Database\Params;
use Engine\Response\Meta\Meta;
use Engine\Response\Meta\Ogp;

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
        $access = new Permission($this->action);
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
        reset($aParams);
        $curr = current($aParams);
        // Book identifier
        $isBook = $curr[0] === 'i';
        if ($isBook) {
            $this->commentTpl = 'Entity/Book/comments';
        }
        // Open comments
        if ($this->input->getGet('/1', null, FILTER_VALIDATE_INT) === 0) {
            $oHelper = $this->commentAction($aParams);

        // Show the book
        } elseif ($isBook) {
            $page = isset($aParams[1]) ? $aParams[1] : '';
            $oHelper = $this->overviewPage(substr($curr, 1), $page, $aParams);

        // List of books
        } else {
            $oHelper = $this->indexNumAction([$curr]);
        }

        return $oHelper;
    }

    protected function overviewPage($id, $page, $aParams)
    {
        $this->request->setParams(array('overview'));

        $oTranslate = \System\Registry::translation();
        $oTranslate->skipUpdate();
        $model = new Model();

        switch ($page) {
            case 'comment':
                $aParams[0] = 0;
                $this->response->title($oTranslate->sys('LB_COMMENTS'), ': ');
                $oHelper = $this->commentAction($aParams);
                break;

            case 'content':
                $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
                $book = $model->getBook($id);
                $this->response->titleOverride(
                    $oTranslate->sys('LB_CONTENT') . ': ' .$book->getContent()
                    . ' (' . $oTranslate->get(['og:title', 'book/overview']) . ')'
                );
                $oHelper->add('Entity/Book/content', array(
                    'entity' => $book,
                    'url' => $book->getPattern()
                ));
                break;

            case 'quote':
                $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
                $book = $model->getBook($id);
                $this->response->titleOverride(
                    $oTranslate->sys('LB_QUOTE') . ': ' .$book->getContent()
                    . ' (' . $oTranslate->get(['og:title', 'book/overview']) . ')'
                );
                $oHelper->add('Entity/Book/quotes', array(
                    'entity' => $book,
                    'url' => $book->getPattern(),
                    'list' => $model->getBookQuotes($book, $id)
                ));
                break;

            default:
                $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
                $data = $model->getBookInfo($id);
                // Update meta for the book (author, cover)
                $data['author'] = $oTranslate->entity(['author', $data['entity']->getPattern()])->getContent();
                foreach (explode(',', $data['author']) as $name) {
                    $this->response->meta(new Meta(Meta::TYPE_AUTHOR, $name), true);
                }
                $this->response->meta(new Ogp(
                    Ogp::TYPE_IMAGE,
                    $oTranslate->get(['og:image', $data['entity']->getPattern()])
                ));
                $oHelper->add('Entity/Book/page', $data);

                $rep = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT);
                $sUrl = $this->input->getUrl(null);
                $oHelper->add('Entity/comments_inline', array(
                    'list' => $rep->findComments($sUrl, 0),
                    'url' => $sUrl,
                    'page' => 1
                ));
        }

        return $oHelper;
    }

    /**
     * List pagination
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexNumAction(array $aParams)
    {
        return $this->searchAction($aParams);
    }

    /**
     * Show topics by using keywords values
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function searchAction(array $aParams)
    {
        $model = new Model();
        // for a link highlighting
        $this->request->setParams(array('overview'));
        $layout = new \Layouts\Helper\Book($this->request, $this->response);

        $oTranslate = \System\Registry::translation();

        $sSearch = $this->input->getGet('/0', '');
        $iPage = $this->input->getGet('/1', 0, FILTER_VALIDATE_INT);
        if ((string)(int)$sSearch === $sSearch) {
            $iPage = $sSearch;
            $sSearch = '';
        }

        $searchParam = $this->input->getPost('search', $sSearch);
        $aList = $model->getPublicationsByPart($searchParam, $iPage);
        if (!$aList['count']) {
            throw new \Error\Validation(
                $oTranslate->sys('LB_HEADER_404'),
                \Defines\Response\Code::E_NOT_FOUND
            );
        }

        $layout->add('index', array(
            'stat' => null,
            'num' => Params::getPageCount($aList['count'], $model->getPageCount()),
            'curr' => $iPage,
            'title' => $searchParam ? $searchParam : $oTranslate->sys('LB_BOOK_OVERVIEW'),
            'desc' => $oTranslate->get(['description', $this->input->getUrl(null)]),
            'url' => $searchParam ? 'book/overview/search/' . $searchParam : 'book/overview',
            'search_action' => 'search',
            'list' => $aList,
            'read_list' => (new \Modules\Book\Calendar\Model)->getAllList(false),
            'search' => $searchParam,
            'language_list' => $searchParam ? null : \Defines\Language::getList()
        ));
        return $layout;
    }

    public function authorAction(array $aParams)
    {
        $oModel = new Model();
        // for a link highlighting
        $this->request->setParams(array('overview'));
        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);

        $oTranslate = \System\Registry::translation();

        $sSearch = $this->input->getGet('/0', '');
        $iPage = $this->input->getGet('/1', 0, FILTER_VALIDATE_INT);
        if ((string)(int)$sSearch === $sSearch) {
            $iPage = $sSearch;
            $sSearch = '';
        }

        $searchParam = $this->input->getPost('search', $sSearch);
        $aList = $oModel->getPublicationsByAuthor($searchParam, $iPage);
        if (!$aList['count']) {
            throw new \Error\Validation(
                $oTranslate->sys('LB_HEADER_404'),
                \Defines\Response\Code::E_NOT_FOUND
            );
        }

        $oHelper->add('index', array(
            'stat' => null,
            'num' => Params::getPageCount($aList['count'], $oModel->getPageCount()),
            'curr' => $iPage,
            'title' => $searchParam ? $searchParam : $oTranslate->sys('LB_BOOK_SEARCH') . ' ' . $oTranslate->sys('LB_BOOK_AUTHOR'),
            'search' => $searchParam,
            'desc' => $oTranslate->get(['description', $this->input->getUrl(null)]),
            'url' => 'book/overview/author/' . $searchParam,
            'search_action' => 'author',
            'list' => $aList,
            'read_list' => (new \Modules\Book\Calendar\Model)->getAllList(false),
            'language' => null
        ));
        return $oHelper;
    }

    public function udcAction(array $aParams)
    {
        $em = \System\Registry::connection();
        $book = $em->find(\Defines\Database\CrMain::CONTENT, filter_var($aParams[0], FILTER_SANITIZE_NUMBER_INT));
        if (!$book) {
            throw new \Error\Validation(
                \Defines\Response\Code::getHeader(\Defines\Response\Code::E_NOT_FOUND),
                \Defines\Response\Code::E_NOT_FOUND
            );
        }
        $isbn = \System\Registry::translation()->entity(['isbn', $book->getPattern()])->getContent();

        $list = new \Engine\Book\Search\BasNet(\System\Registry::translation()->getTargetLanguage());
        $bookList = new \Engine\Book\Result\BookList([]);
        foreach ($list->fill($bookList, [\Engine\Book\Search::TYPE_ISBN => $isbn]) as $book) {
            if ($book->getUdc()) {
                $udc = \System\Registry::translation()->entity(['udc', $book->getPattern()]);
                $udc->setContent($book->getUdc());
                $em->persist($udc);
                $em->flush($udc);
                break;
            }
        }
        return $this->indexAction($aParams);
    }
}
